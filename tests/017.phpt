--TEST--
call_user_func_map() default argument.
--FILE--
<?php
if (!extension_loaded('callmap')) {
    dl('callmap.' . PHP_SHLIB_SUFFIX);
}

function foobar($a = 'foo', $b = 'bar') {
    echo __FUNCTION__, " got $a and $b\n";
    return $a . $b;
}

echo "----- Calling 1 -----\n";
$params = array('one');
$ret = call_user_func_map('foobar', $params);
var_dump($params);
var_dump($ret);

echo "----- Calling 2 -----\n";
$params = array('a' => 'one');
$ret = call_user_func_map('foobar', $params);
var_dump($params);
var_dump($ret);

echo "----- Calling 3 -----\n";
$params = array('b' => 'two');
$ret = call_user_func_map('foobar', $params);
var_dump($params);
var_dump($ret);

echo "----- Calling 4 -----\n";
$params = array('a' => 'one', 'c' => 'two');
$ret = call_user_func_map('foobar', $params);
var_dump($params);
var_dump($ret);

echo "----- Calling 5 -----\n";
$params = array('c' => 'one', 'b' => 'two');
$ret = call_user_func_map('foobar', $params);
var_dump($params);
var_dump($ret);

echo "----- Calling 6 -----\n";
$params = array('c' => 'one', 'd' => 'two');
$ret = call_user_func_map('foobar', $params);
var_dump($params);
var_dump($ret);

echo "----- Calling 7 -----\n";
$params = array('A' => 'one', 'B' => 'two');
$ret = call_user_func_map('foobar', $params);
var_dump($params);
var_dump($ret);

echo "----- Calling 8 -----\n";
$params = array();
$ret = call_user_func_map('foobar', $params);
var_dump($params);
var_dump($ret);
?>
--EXPECTF--
----- Calling 1 -----
foobar got one and bar
array(1) {
  [0]=>
  string(3) "one"
}
string(6) "onebar"
----- Calling 2 -----
foobar got one and bar
array(1) {
  ["a"]=>
  string(3) "one"
}
string(6) "onebar"
----- Calling 3 -----
foobar got  and two
array(1) {
  ["b"]=>
  string(3) "two"
}
string(3) "two"
----- Calling 4 -----
foobar got one and bar
array(2) {
  ["a"]=>
  string(3) "one"
  ["c"]=>
  string(3) "two"
}
string(6) "onebar"
----- Calling 5 -----
foobar got  and two
array(2) {
  ["c"]=>
  string(3) "one"
  ["b"]=>
  string(3) "two"
}
string(3) "two"
----- Calling 6 -----
foobar got foo and bar
array(2) {
  ["c"]=>
  string(3) "one"
  ["d"]=>
  string(3) "two"
}
string(6) "foobar"
----- Calling 7 -----
foobar got foo and bar
array(2) {
  ["A"]=>
  string(3) "one"
  ["B"]=>
  string(3) "two"
}
string(6) "foobar"
----- Calling 8 -----
foobar got foo and bar
array(0) {
}
string(6) "foobar"
