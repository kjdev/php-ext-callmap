--TEST--
call_user_func_map() passes by reference if the array element is referenced, regardless of function signature.
--FILE--
<?php
if (!extension_loaded('callmap')) {
    dl('callmap.' . PHP_SHLIB_SUFFIX);
}

function by_val($arg) {
    $arg = 'changed';
}

function by_ref(&$arg) {
    $arg = 'changed';
}

echo "------ Calling by_val() with unreferenced argument ------\n";
$arg = array('original');
call_user_func_map('by_val', $arg);
var_dump($arg);

echo "------ Calling by_ref() with unreferenced argument ------\n";
$arg = array('original');
call_user_func_map('by_ref', $arg);
var_dump($arg);

echo "------ Calling by_val() with referenced argument ------\n";
$arg = array('original');
$ref = &$arg[0];
call_user_func_map('by_val', $arg);
var_dump($arg);

echo "------ Calling by_ref() with referenced argument ------\n";
$arg = array('original');
$ref = &$arg[0];
call_user_func_map('by_ref', $arg);
var_dump($arg);

?>
--EXPECTF--
------ Calling by_val() with unreferenced argument ------
array(1) {
  [0]=>
  string(8) "original"
}
------ Calling by_ref() with unreferenced argument ------

Warning: Parameter 1 to by_ref() expected to be a reference, value given in %s on line %d
array(1) {
  [0]=>
  string(8) "original"
}
------ Calling by_val() with referenced argument ------
array(1) {
  [0]=>
  &string(8) "original"
}
------ Calling by_ref() with referenced argument ------
array(1) {
  [0]=>
  &string(7) "changed"
}
