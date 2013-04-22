--TEST--
call_user_func_map() default argument 2.
--FILE--
<?php
if (!extension_loaded('callmap')) {
    dl('callmap.' . PHP_SHLIB_SUFFIX);
}

function test($a = 'one', $b = 'two', $c = 'three') {
    echo __FUNCTION__, " got $a , $b and $c\n";
    return $a . $b . $c;
}

echo "----- Calling 1 -----\n";
$params = array();
var_dump($params);
$ret = call_user_func_map('test', $params);
var_dump($ret);

echo "----- Calling 2 -----\n";
$params = array('1');
var_dump($params);
$ret = call_user_func_map('test', $params);
var_dump($ret);

echo "----- Calling 3 -----\n";
$params = array('a' => '1');
var_dump($params);
$ret = call_user_func_map('test', $params);
var_dump($ret);

echo "----- Calling 4 -----\n";
$params = array('b' => '2');
var_dump($params);
$ret = call_user_func_map('test', $params);
var_dump($ret);

echo "----- Calling 5 -----\n";
$params = array('c' => '3');
var_dump($params);
$ret = call_user_func_map('test', $params);
var_dump($ret);

echo "----- Calling 6 -----\n";
$params = array('a' => '1', 'b' => '2');
var_dump($params);
$ret = call_user_func_map('test', $params);
var_dump($ret);

echo "----- Calling 7 -----\n";
$params = array('b' => '2', 'c' => '3');
var_dump($params);
$ret = call_user_func_map('test', $params);
var_dump($ret);

echo "----- Calling 8 -----\n";
$params = array('a' => '1', 'c' => '3');
var_dump($params);
$ret = call_user_func_map('test', $params);
var_dump($ret);

echo "----- Calling 9 -----\n";
$params = array('d' => '4');
var_dump($params);
$ret = call_user_func_map('test', $params);
var_dump($ret);

echo "----- Calling 10 -----\n";
$params = array('d' => '4', 'a' => '1');
var_dump($params);
$ret = call_user_func_map('test', $params);
var_dump($ret);

echo "----- Calling 11 -----\n";
$params = array('d' => '4', 'b' => '2');
var_dump($params);
$ret = call_user_func_map('test', $params);
var_dump($ret);

echo "----- Calling 12 -----\n";
$params = array('d' => '4', 'c' => '3');
var_dump($params);
$ret = call_user_func_map('test', $params);
var_dump($ret);

echo "----- Calling 13 -----\n";
$params = array('d' => '4', 'e' => '5');
var_dump($params);
$ret = call_user_func_map('test', $params);
var_dump($ret);
?>
--EXPECTF--
----- Calling 1 -----
array(0) {
}
test got one , two and three
string(11) "onetwothree"
----- Calling 2 -----
array(1) {
  [0]=>
  string(1) "1"
}
test got 1 , two and three
string(9) "1twothree"
----- Calling 3 -----
array(1) {
  ["a"]=>
  string(1) "1"
}
test got 1 , two and three
string(9) "1twothree"
----- Calling 4 -----
array(1) {
  ["b"]=>
  string(1) "2"
}
test got one , 2 and three
string(9) "one2three"
----- Calling 5 -----
array(1) {
  ["c"]=>
  string(1) "3"
}
test got one , two and 3
string(7) "onetwo3"
----- Calling 6 -----
array(2) {
  ["a"]=>
  string(1) "1"
  ["b"]=>
  string(1) "2"
}
test got 1 , 2 and three
string(7) "12three"
----- Calling 7 -----
array(2) {
  ["b"]=>
  string(1) "2"
  ["c"]=>
  string(1) "3"
}
test got one , 2 and 3
string(5) "one23"
----- Calling 8 -----
array(2) {
  ["a"]=>
  string(1) "1"
  ["c"]=>
  string(1) "3"
}
test got 1 , two and 3
string(5) "1two3"
----- Calling 9 -----
array(1) {
  ["d"]=>
  string(1) "4"
}
test got one , two and three
string(11) "onetwothree"
----- Calling 10 -----
array(2) {
  ["d"]=>
  string(1) "4"
  ["a"]=>
  string(1) "1"
}
test got 1 , two and three
string(9) "1twothree"
----- Calling 11 -----
array(2) {
  ["d"]=>
  string(1) "4"
  ["b"]=>
  string(1) "2"
}
test got one , 2 and three
string(9) "one2three"
----- Calling 12 -----
array(2) {
  ["d"]=>
  string(1) "4"
  ["c"]=>
  string(1) "3"
}
test got one , two and 3
string(7) "onetwo3"
----- Calling 13 -----
array(2) {
  ["d"]=>
  string(1) "4"
  ["e"]=>
  string(1) "5"
}
test got one , two and three
string(11) "onetwothree"
