--TEST--
call_user_func_map() passes by reference if the array element is referenced, regardless of function signature.
--SKIPIF--
<?php
if (version_compare(PHP_VERSION, '5.4.0') < 0) {
    die("skip this test is for PHP 5.4.0 or newer");
}
?>
--FILE--
<?php
if (!extension_loaded('callmap')) {
    dl('callmap.' . PHP_SHLIB_SUFFIX);
}

function by_val($arg) {
    $arg = 'changed';
}

echo "------ Calling by_val() with unreferenced argument ------\n";
$arg = array('original');
call_user_func_map('by_val', $arg);
var_dump($arg);

echo "------ Calling by_val() with referenced argument ------\n";
$arg = array('original');
$ref = &$arg[0];
call_user_func_map('by_val', $arg);
var_dump($arg);

?>
--EXPECTF--
------ Calling by_val() with unreferenced argument ------
array(1) {
  [0]=>
  string(8) "original"
}
------ Calling by_val() with referenced argument ------
array(1) {
  [0]=>
  &string(8) "original"
}
