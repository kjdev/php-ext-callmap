--TEST--
call_user_func_map() static method called.
--FILE--
<?php
if (!extension_loaded('callmap')) {
    dl('callmap.' . PHP_SHLIB_SUFFIX);
}

class foo {
    static public function bar($a, $b) {
        echo __METHOD__, " got $a and $b\n";
        return $a . $b;
    }
}

echo "----- Calling 1 -----\n";
$params = array('one', 'two');
$ret = call_user_func_map(array('foo', 'bar'), $params);
var_dump($params);
var_dump($ret);

echo "----- Calling 2 -----\n";
$params = array('a' => 'one', 'b' => 'two');
$ret = call_user_func_map(array('foo', 'bar'), $params);
var_dump($params);
var_dump($ret);

echo "----- Calling 3 -----\n";
$params = array('b' => 'two', 'a' => 'one');
$ret = call_user_func_map(array('foo', 'bar'), $params);
var_dump($params);
var_dump($ret);

echo "----- Calling 4 -----\n";
$params = array('one', 'b' => 'two');
$ret = call_user_func_map(array('foo', 'bar'), $params);
var_dump($params);
var_dump($ret);

echo "----- Calling 5 -----\n";
$params = array('a' => 'one', 'two');
$ret = call_user_func_map(array('foo', 'bar'), $params);
var_dump($params);
var_dump($ret);

echo "----- Calling 6 -----\n";
$params = array('b' => 'two', 'one');
$ret = call_user_func_map(array('foo', 'bar'), $params);
var_dump($params);
var_dump($ret);

echo "----- Calling 7 -----\n";
$params = array('a' => 'one', 'b' => 'two', 'c' => 'three');
$ret = call_user_func_map(array('foo', 'bar'), $params);
var_dump($params);
var_dump($ret);

echo "----- Calling 8 -----\n";
$params = array('c' => 'three', 'b' => 'two', 'a' => 'one');
$ret = call_user_func_map(array('foo', 'bar'), $params);
var_dump($params);
var_dump($ret);
?>
--EXPECTF--
----- Calling 1 -----
foo::bar got one and two
array(2) {
  [0]=>
  string(3) "one"
  [1]=>
  string(3) "two"
}
string(6) "onetwo"
----- Calling 2 -----
foo::bar got one and two
array(2) {
  ["a"]=>
  string(3) "one"
  ["b"]=>
  string(3) "two"
}
string(6) "onetwo"
----- Calling 3 -----
foo::bar got one and two
array(2) {
  ["b"]=>
  string(3) "two"
  ["a"]=>
  string(3) "one"
}
string(6) "onetwo"
----- Calling 4 -----
foo::bar got one and two
array(2) {
  [0]=>
  string(3) "one"
  ["b"]=>
  string(3) "two"
}
string(6) "onetwo"
----- Calling 5 -----
foo::bar got one and two
array(2) {
  ["a"]=>
  string(3) "one"
  [0]=>
  string(3) "two"
}
string(6) "onetwo"
----- Calling 6 -----
foo::bar got one and two
array(2) {
  ["b"]=>
  string(3) "two"
  [0]=>
  string(3) "one"
}
string(6) "onetwo"
----- Calling 7 -----
foo::bar got one and two
array(3) {
  ["a"]=>
  string(3) "one"
  ["b"]=>
  string(3) "two"
  ["c"]=>
  string(5) "three"
}
string(6) "onetwo"
----- Calling 8 -----
foo::bar got one and two
array(3) {
  ["c"]=>
  string(5) "three"
  ["b"]=>
  string(3) "two"
  ["a"]=>
  string(3) "one"
}
string(6) "onetwo"
