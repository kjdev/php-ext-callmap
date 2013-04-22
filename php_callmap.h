
#ifndef PHP_CALLMAP_H
#define PHP_CALLMAP_H

#define CALLMAP_NAMESPACE "callmap"
#define CALLMAP_EXT_VERSION "0.2.0"

extern zend_module_entry callmap_module_entry;
#define phpext_callmap_ptr &callmap_module_entry

#ifdef PHP_WIN32
#    define PHP_CALLMAP_API __declspec(dllexport)
#elif defined(__GNUC__) && __GNUC__ >= 4
#    define PHP_CALLMAP_API __attribute__ ((visibility("default")))
#else
#    define PHP_CALLMAP_API
#endif

#ifdef ZTS
#    include "TSRM.h"
#endif

ZEND_BEGIN_MODULE_GLOBALS(callmap)
zend_bool call_user_func_array;
zend_bool forward_static_call_array;
ZEND_END_MODULE_GLOBALS(callmap)

#ifdef ZTS
#    define CALLMAP_G(v) TSRMG(callmap_globals_id, zend_callmap_globals *, v)
#else
#    define CALLMAP_G(v) (callmap_globals.v)
#endif

#endif  /* PHP_CALLMAP_H */
