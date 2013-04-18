--TEST--
call_user_func_map() lambda function called.
--SKIPIF--
<?php
if (version_compare(PHP_VERSION, '5.3.0') < 0) {
    die("skip this test is for PHP 5.3.0 or newer");
}
?>
--FILE--
<?php
if (!extension_loaded('callmap')) {
    dl('callmap.' . PHP_SHLIB_SUFFIX);
}

$func = function($a, $b) {
    echo __FUNCTION__, " got $a and $b\n";
    return $a . $b;
};

echo "----- Calling 1 -----\n";
$params = array('one', 'two');
var_dump($params);
$ret = call_user_func_map($func, $params);
var_dump($ret);

echo "----- Calling 2 -----\n";
$params = array('a' => 'one', 'b' => 'two');
var_dump($params);
$ret = call_user_func_map($func, $params);
var_dump($ret);

echo "----- Calling 3 -----\n";
$params = array('b' => 'two', 'a' => 'one');
var_dump($params);
$ret = call_user_func_map($func, $params);
var_dump($ret);

echo "----- Calling 4 -----\n";
$params = array('one', 'b' => 'two');
var_dump($params);
$ret = call_user_func_map($func, $params);
var_dump($ret);

echo "----- Calling 5 -----\n";
$params = array('a' => 'one', 'two');
var_dump($params);
$ret = call_user_func_map($func, $params);
var_dump($ret);

echo "----- Calling 6 -----\n";
$params = array('b' => 'two', 'one');
var_dump($params);
$ret = call_user_func_map($func, $params);
var_dump($ret);

echo "----- Calling 7 -----\n";
$params = array('a' => 'one', 'b' => 'two', 'c' => 'three');
var_dump($params);
$ret = call_user_func_map($func, $params);
var_dump($ret);

echo "----- Calling 8 -----\n";
$params = array('c' => 'three', 'b' => 'two', 'a' => 'one');
var_dump($params);
$ret = call_user_func_map($func, $params);
var_dump($ret);
?>
--EXPECTF--
----- Calling 1 -----
array(2) {
  [0]=>
  string(3) "one"
  [1]=>
  string(3) "two"
}
{closure} got one and two
string(6) "onetwo"
----- Calling 2 -----
array(2) {
  ["a"]=>
  string(3) "one"
  ["b"]=>
  string(3) "two"
}
{closure} got one and two
string(6) "onetwo"
----- Calling 3 -----
array(2) {
  ["b"]=>
  string(3) "two"
  ["a"]=>
  string(3) "one"
}
{closure} got one and two
string(6) "onetwo"
----- Calling 4 -----
array(2) {
  [0]=>
  string(3) "one"
  ["b"]=>
  string(3) "two"
}
{closure} got one and two
string(6) "onetwo"
----- Calling 5 -----
array(2) {
  ["a"]=>
  string(3) "one"
  [0]=>
  string(3) "two"
}
{closure} got one and two
string(6) "onetwo"
----- Calling 6 -----
array(2) {
  ["b"]=>
  string(3) "two"
  [0]=>
  string(3) "one"
}
{closure} got one and two
string(6) "onetwo"
----- Calling 7 -----
array(3) {
  ["a"]=>
  string(3) "one"
  ["b"]=>
  string(3) "two"
  ["c"]=>
  string(5) "three"
}
{closure} got one and two
string(6) "onetwo"
----- Calling 8 -----
array(3) {
  ["c"]=>
  string(5) "three"
  ["b"]=>
  string(3) "two"
  ["a"]=>
  string(3) "one"
}
{closure} got one and two
string(6) "onetwo"
