
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

typedef struct callmap_var_t {
    zval *null;
    zval *defaults;
} callmap_var;

static void
callmap_var_init(callmap_var *arg) {
    MAKE_STD_ZVAL(arg->defaults);
    array_init(arg->defaults);

    MAKE_STD_ZVAL(arg->null);
    ZVAL_NULL(arg->null);
}

static void
callmap_var_destroy(callmap_var *arg) {
    zval **zv;
    HashPosition pos;

    zend_hash_internal_pointer_reset_ex(Z_ARRVAL_P(arg->defaults), &pos);
    while (zend_hash_get_current_data_ex(Z_ARRVAL_P(arg->defaults),
                                         (void **)&zv, &pos) == SUCCESS) {
        zval_ptr_dtor(zv);
        zend_hash_move_forward_ex(Z_ARRVAL_P(arg->defaults), &pos);
    }

    zval_ptr_dtor(&arg->defaults);
    zval_ptr_dtor(&arg->null);
}

static zval **
callmap_get_default(zend_function *func, long offset, callmap_var *var TSRMLS_DC)
{
    zend_op_array *op_array;
    zend_op *op, *end;
    zval *zv, **zv_ptr;

    if (!func) {
        return NULL;
    }

    op_array = (zend_op_array *)func;
    op = op_array->opcodes;
    end = op + op_array->last;
    ++offset;

    while (op < end) {
#if ZEND_MODULE_API_NO < 20100525
        if ((op->opcode == ZEND_RECV || op->opcode == ZEND_RECV_INIT)
            && op->op1.u.constant.value.lval == offset) {
            if (Z_TYPE(op->op2.u.constant) == IS_NULL) {
                return NULL;
            }
            MAKE_STD_ZVAL(zv);
            *zv = op->op2.u.constant;
            zval_copy_ctor(zv);
#else
        if ((op->opcode == ZEND_RECV || op->opcode == ZEND_RECV_INIT)
            && op->op1.num == offset) {
            if (op->opcode != ZEND_RECV_INIT || op->op2_type == IS_UNUSED) {
                return NULL;
            }
            MAKE_STD_ZVAL(zv);
            *zv = *op->op2.zv;
            zval_copy_ctor(zv);
            Z_UNSET_ISREF_P(zv);
#endif
            zend_hash_next_index_insert(Z_ARRVAL_P(var->defaults), &zv,
                                        sizeof(zval *), (void **)&zv_ptr);

            return zv_ptr;
        }
        ++op;
    }

    return NULL;
}

static int
callmap_fcall_info_args(zend_fcall_info *fci, zval *args, zend_function *func,
                        callmap_var *var TSRMLS_DC)
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
                    fci->params[i] = callmap_get_default(func, i, var TSRMLS_CC);
                    if (fci->params[i] != NULL) {
                        count++;
                    } else {
                        fci->params[i] = &var->null;
                        offset++;
                    }
                }
            } else {
                if (zend_hash_index_find(Z_ARRVAL_P(args), index,
                                         (void **)&fci->params[i]) == SUCCESS) {
                    count = count + offset + 1;
                    offset = 0;
                    index++;
                    argc--;
                } else {
                    fci->params[i] = callmap_get_default(func, i, var TSRMLS_CC);
                    if (fci->params[i] != NULL) {
                        count++;
                    } else {
                        fci->params[i] = &var->null;
                        offset++;
                    }
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
    zval *params, *retval_ptr = NULL;
    zend_fcall_info fci;
    zend_fcall_info_cache fci_cache;
    callmap_var var;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
                              "fa/", &fci, &fci_cache, &params) == FAILURE) {
        return;
    }

    callmap_var_init(&var);

    callmap_fcall_info_args(&fci, params,
                            fci_cache.function_handler, &var TSRMLS_CC);

    fci.retval_ptr_ptr = &retval_ptr;

    if (zend_call_function(&fci, &fci_cache TSRMLS_CC) == SUCCESS
        && fci.retval_ptr_ptr
        && *fci.retval_ptr_ptr) {
        COPY_PZVAL_TO_ZVAL(*return_value, *fci.retval_ptr_ptr);
    }

    zend_fcall_info_args_clear(&fci, 1);

    callmap_var_destroy(&var);
}

ZEND_FUNCTION(forward_static_call_map)
{
    zval *params, *retval_ptr = NULL;
    zend_fcall_info fci;
    zend_fcall_info_cache fci_cache;
    callmap_var var;

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

    callmap_var_init(&var);

    callmap_fcall_info_args(&fci, params,
                            fci_cache.function_handler, &var TSRMLS_CC);

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

    callmap_var_destroy(&var);
}

ZEND_DECLARE_MODULE_GLOBALS(callmap)

PHP_INI_BEGIN()
STD_PHP_INI_ENTRY("callmap.override_call_user_func_array", "0",
                  PHP_INI_SYSTEM, OnUpdateBool, call_user_func_array,
                  zend_callmap_globals, callmap_globals)
STD_PHP_INI_ENTRY("callmap.override_forward_static_call_array", "0",
                  PHP_INI_SYSTEM, OnUpdateBool, forward_static_call_array,
                  zend_callmap_globals, callmap_globals)
PHP_INI_END()

zend_function *origin_call_user_func_array = NULL;
zend_function *origin_forward_static_call_array = NULL;

static zend_function *
callmap_override_function(char *origin, char *override TSRMLS_DC)
{
    size_t origin_len = strlen(origin);
    size_t override_len = strlen(override);
    zend_function *origin_fe, *override_fe;

    if (zend_hash_find(CG(function_table), override, override_len + 1,
                       (void **)&override_fe) == FAILURE) {
        zend_error(E_WARNING, "%s symbol not found.", override);
        return NULL;
    }

    if (zend_hash_find(CG(function_table), origin, origin_len + 1,
                       (void **)&origin_fe) == FAILURE) {
        zend_error(E_WARNING, "%s symbol not found.", origin);
        return NULL;
    }

    if (zend_hash_update(CG(function_table), origin, origin_len + 1,
                         (void *)override_fe, sizeof(zend_function),
                         NULL) == FAILURE) {
        zend_error(E_WARNING, "Error override reference to function name %s()",
                   origin);
        return NULL;
    }

    function_add_ref(override_fe);

    return origin_fe;
}

static void
callmap_init_globals(zend_callmap_globals *callmap_globals)
{
    callmap_globals->call_user_func_array = 0;
    callmap_globals->forward_static_call_array = 0;
}

ZEND_MINIT_FUNCTION(callmap)
{
    ZEND_INIT_MODULE_GLOBALS(callmap, callmap_init_globals, NULL);
    REGISTER_INI_ENTRIES();

    /* Function override */
    if (CALLMAP_G(call_user_func_array)) {
        origin_call_user_func_array = callmap_override_function(
            "call_user_func_array", "call_user_func_map" TSRMLS_CC);
    }
    if (CALLMAP_G(forward_static_call_array)) {
        origin_forward_static_call_array = callmap_override_function(
            "forward_static_call_array", "forward_static_call_map" TSRMLS_CC);
    }

    return SUCCESS;
}

ZEND_MSHUTDOWN_FUNCTION(callmap)
{
    UNREGISTER_INI_ENTRIES();
    return SUCCESS;
}

ZEND_MINFO_FUNCTION(callmap)
{
    php_info_print_table_start();
    php_info_print_table_row(2, "Call Map support", "enabled");
    php_info_print_table_row(2, "Extension Version", CALLMAP_EXT_VERSION);
    if (CALLMAP_G(call_user_func_array)) {
        php_info_print_table_row(2, "Override call_user_func_array",
                                 "enabled");
    }
    if (CALLMAP_G(forward_static_call_array)) {
        php_info_print_table_row(2, "Override forward_static_call_array",
                                 "enabled");
    }
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
    ZEND_MINIT(callmap),
    ZEND_MSHUTDOWN(callmap),
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
