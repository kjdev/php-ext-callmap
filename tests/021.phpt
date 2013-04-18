--TEST--
call_user_func_map() argument types called.
--FILE--
<?php
if (!extension_loaded('callmap')) {
    dl('callmap.' . PHP_SHLIB_SUFFIX);
}

function foobar($a, $b) {
    var_dump($a);
    var_dump($b);
}

echo "----- Argument array -----\n";
$params = array('a' => array('one'), 'b' => array('two', 'three'));
var_dump($params);
call_user_func_map('foobar', $params);

echo "----- Argument array 2 -----\n";
$params = array('a' => array('x' => 'one'), 'b' => array('y' => 'two', 'z' => 'three'));
var_dump($params);
call_user_func_map('foobar', $params);

echo "----- Argument null -----\n";
$params = array('a' => null, 'b' => null);
var_dump($params);
call_user_func_map('foobar', $params);

echo "----- Argument int -----\n";
$params = array('a' => 123, 'b' => -456);
var_dump($params);
call_user_func_map('foobar', $params);

echo "----- Argument class -----\n";
class Test {
    public $a = 123;
}
$a = new Test;
$b = new stdClass;
$b->a = 456;
$params = array('a' => $a, 'b' => $b);
var_dump($params);
call_user_func_map('foobar', $params);
?>
--EXPECTF--
----- Argument array -----
array(2) {
  ["a"]=>
  array(1) {
    [0]=>
    string(3) "one"
  }
  ["b"]=>
  array(2) {
    [0]=>
    string(3) "two"
    [1]=>
    string(5) "three"
  }
}
array(1) {
  [0]=>
  string(3) "one"
}
array(2) {
  [0]=>
  string(3) "two"
  [1]=>
  string(5) "three"
}
----- Argument array 2 -----
array(2) {
  ["a"]=>
  array(1) {
    ["x"]=>
    string(3) "one"
  }
  ["b"]=>
  array(2) {
    ["y"]=>
    string(3) "two"
    ["z"]=>
    string(5) "three"
  }
}
array(1) {
  ["x"]=>
  string(3) "one"
}
array(2) {
  ["y"]=>
  string(3) "two"
  ["z"]=>
  string(5) "three"
}
----- Argument null -----
array(2) {
  ["a"]=>
  NULL
  ["b"]=>
  NULL
}
NULL
NULL
----- Argument int -----
array(2) {
  ["a"]=>
  int(123)
  ["b"]=>
  int(-456)
}
int(123)
int(-456)
----- Argument class -----
array(2) {
  ["a"]=>
  object(Test)#%d (1) {
    ["a"]=>
    int(123)
  }
  ["b"]=>
  object(stdClass)#%d (1) {
    ["a"]=>
    int(456)
  }
}
object(Test)#%d (1) {
  ["a"]=>
  int(123)
}
object(stdClass)#%d (1) {
  ["a"]=>
  int(456)
}
