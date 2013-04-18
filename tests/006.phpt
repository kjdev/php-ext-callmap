--TEST--
call_user_func(): Wrong parameters
--FILE--
<?php
if (!extension_loaded('callmap')) {
    dl('callmap.' . PHP_SHLIB_SUFFIX);
}

call_user_func_map(array('Foo', 'bar'), array());
call_user_func_map(array(NULL, 'bar'), array());
call_user_func_map(array('stdclass', NULL), array());

?>
--EXPECTF--
Warning: call_user_func_map() expects parameter 1 to be a valid callback, class 'Foo' not found in %s on line %d

Warning: call_user_func_map() expects parameter 1 to be a valid callback, first array member is not a valid class name or object in %s on line %d

Warning: call_user_func_map() expects parameter 1 to be a valid callback, second array member is not a valid method in %s on line %d
