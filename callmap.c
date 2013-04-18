
#ifdef HAVE_CONFIG_H
#    include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "php_verdep.h"

#include "php_callmap.h"

ZEND_BEGIN_ARG_INFO_EX(arginfo_call_user_func_map, 0, 0, 2)
    ZEND_ARG_INFO(0, callback)
    ZEND_ARG_INFO(0, params)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_forward_static_call_map, 0, 0, 2)
    ZEND_ARG_INFO(0, callback)
    ZEND_ARG_INFO(0, params)
ZEND_END_ARG_INFO()

static int
callmap_fcall_info_args(zend_fcall_info *fci, zval *args,
                        zend_function *func, zval **null TSRMLS_DC)
{
    long argc;
    zval **arg, ***params;

    zend_fcall_info_args_clear(fci, !args);

    if (!args) {
        return SUCCESS;
    }

    if (Z_TYPE_P(args) != IS_ARRAY) {
        return FAILURE;
    }

    argc = zend_hash_num_elements(Z_ARRVAL_P(args));

    if (argc == 0) {
        return SUCCESS;
    }

    if (func && func->common.arg_info && func->common.num_args > 0) {
        zend_arg_info *arg_info = func->common.arg_info;
        long i, offset = 0, count = 0, index = 0;

        fci->params = (zval ***)erealloc(fci->params,
                                         func->common.num_args * sizeof(zval**));

        for (i = 0; i < func->common.num_args; i++) {
            if (zend_hash_exists(Z_ARRVAL_P(args),
                                 arg_info->name, arg_info->name_len+1)) {
                if (zend_hash_find(Z_ARRVAL_P(args),
                                   arg_info->name, arg_info->name_len+1,
                                   (void **)&fci->params[i]) == SUCCESS) {
                    count = count + offset + 1;
                    offset = 0;
                    argc--;
                } else {
                    fci->params[i] = null;
                    offset++;
                }
            } else {
                if (zend_hash_index_find(Z_ARRVAL_P(args), index,
                                         (void **)&fci->params[i]) == SUCCESS) {
                    count = count + offset + 1;
                    offset = 0;
                    index++;
                    argc--;
                } else {
                    fci->params[i] = null;
                    offset++;
                }
            }
            arg_info++;

            if (argc == 0) {
                break;
            }
        }

        fci->param_count = count;
    } else {
        HashPosition pos;

        fci->param_count = argc;
        params = (zval ***)erealloc(fci->params,
                                    fci->param_count * sizeof(zval **));
        fci->params = params;
        zend_hash_internal_pointer_reset_ex(Z_ARRVAL_P(args), &pos);
        while (zend_hash_get_current_data_ex(Z_ARRVAL_P(args),
                                             (void *)&arg, &pos) == SUCCESS) {
            *params++ = arg;
            zend_hash_move_forward_ex(Z_ARRVAL_P(args), &pos);
        }
    }

    return SUCCESS;
}

ZEND_FUNCTION(call_user_func_map)
{
    zval *params, *retval_ptr = NULL, *null = NULL;
    zend_fcall_info fci;
    zend_fcall_info_cache fci_cache;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
                              "fa/", &fci, &fci_cache, &params) == FAILURE) {
        return;
    }

    MAKE_STD_ZVAL(null);
    ZVAL_NULL(null);

    callmap_fcall_info_args(&fci, params,
                            fci_cache.function_handler, &null TSRMLS_CC);

    fci.retval_ptr_ptr = &retval_ptr;

    if (zend_call_function(&fci, &fci_cache TSRMLS_CC) == SUCCESS
        && fci.retval_ptr_ptr
        && *fci.retval_ptr_ptr) {
        COPY_PZVAL_TO_ZVAL(*return_value, *fci.retval_ptr_ptr);
    }

    zend_fcall_info_args_clear(&fci, 1);

    zval_ptr_dtor(&null);
}

ZEND_FUNCTION(forward_static_call_map)
{
    zval *params, *retval_ptr = NULL, *null = NULL;
    zend_fcall_info fci;
    zend_fcall_info_cache fci_cache;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
                              "fa/", &fci, &fci_cache, &params) == FAILURE) {
        return;
    }

    /*
    //forward_static_call
    if (!EG(active_op_array)->scope) {
        zend_error(E_ERROR,
                   "Cannot call forward_static_call_map() "
                   "when no class scope is active");
    }
    */

    MAKE_STD_ZVAL(null);
    ZVAL_NULL(null);

    callmap_fcall_info_args(&fci, params,
                            fci_cache.function_handler, &null TSRMLS_CC);

    fci.retval_ptr_ptr = &retval_ptr;

    if (EG(called_scope)
        && instanceof_function(EG(called_scope),
                               fci_cache.calling_scope TSRMLS_CC)) {
        fci_cache.called_scope = EG(called_scope);
    }

    if (zend_call_function(&fci, &fci_cache TSRMLS_CC) == SUCCESS
        && fci.retval_ptr_ptr && *fci.retval_ptr_ptr) {
        COPY_PZVAL_TO_ZVAL(*return_value, *fci.retval_ptr_ptr);
    }

    zend_fcall_info_args_clear(&fci, 1);

    zval_ptr_dtor(&null);
}

ZEND_MINFO_FUNCTION(callmap)
{
    php_info_print_table_start();
    php_info_print_table_row(2, "Call Map support", "enabled");
    php_info_print_table_row(2, "Extension Version", CALLMAP_EXT_VERSION);
    php_info_print_table_end();
}

static zend_function_entry callmap_functions[] = {
    ZEND_FE(call_user_func_map, arginfo_call_user_func_map)
    ZEND_FE(forward_static_call_map, arginfo_forward_static_call_map)
    ZEND_FE_END
};

zend_module_entry callmap_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
    STANDARD_MODULE_HEADER,
#endif
    "callmap",
    callmap_functions,
    NULL,
    NULL,
    NULL,
    NULL,
    ZEND_MINFO(callmap),
#if ZEND_MODULE_API_NO >= 20010901
    CALLMAP_EXT_VERSION,
#endif
    STANDARD_MODULE_PROPERTIES
};

#ifdef COMPILE_DL_CALLMAP
ZEND_GET_MODULE(callmap)
#endif
