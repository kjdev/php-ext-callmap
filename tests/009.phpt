--TEST--
call_user_func_map() leaks on failure
--FILE--
<?php
if (!extension_loaded('callmap')) {
    dl('callmap.' . PHP_SHLIB_SUFFIX);
}

$a = array(4,3,2);

var_dump(call_user_func_map("sort", array($a)));
var_dump(call_user_func_map("strlen", array($a)));

echo "Done\n";
?>
--EXPECTF--
Warning: Parameter 1 to sort() expected to be a reference, value given in %s on line %d
NULL

Warning: strlen() expects parameter 1 to be string, array given in %s on line %d
NULL
Done
