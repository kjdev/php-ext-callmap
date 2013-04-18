--TEST--
call_user_func_map() invalid arguments called.
--FILE--
<?php
ini_set('error_reporting', E_ALL);

if (!extension_loaded('callmap')) {
    dl('callmap.' . PHP_SHLIB_SUFFIX);
}

function foobar($a, $b) {
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

Warning: Missing argument 2 for foobar() in %s on line %d
foobar
Notice: Undefined variable: b in %s on line %d
 got one and 

Notice: Undefined variable: b in %s on line %d
array(1) {
  [0]=>
  string(3) "one"
}
string(3) "one"
----- Calling 2 -----

Warning: Missing argument 2 for foobar() in %s on line %d
foobar
Notice: Undefined variable: b in %s on line %d
 got one and 

Notice: Undefined variable: b in %s on line %d
array(1) {
  ["a"]=>
  string(3) "one"
}
string(3) "one"
----- Calling 3 -----
foobar got  and two
array(1) {
  ["b"]=>
  string(3) "two"
}
string(3) "two"
----- Calling 4 -----

Warning: Missing argument 2 for foobar() in %s.php on line %d
foobar
Notice: Undefined variable: b in %s on line %d
 got one and 

Notice: Undefined variable: b in %s on line %d
array(2) {
  ["a"]=>
  string(3) "one"
  ["c"]=>
  string(3) "two"
}
string(3) "one"
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

Warning: Missing argument 1 for foobar() in %s.php on line %d

Warning: Missing argument 2 for foobar() in %s.php on line %d
foobar
Notice: Undefined variable: a in %s on line %d

Notice: Undefined variable: b in %s on line %d
 got  and 

Notice: Undefined variable: b in %s on line %d

Notice: Undefined variable: a in %s on line %d
array(2) {
  ["c"]=>
  string(3) "one"
  ["d"]=>
  string(3) "two"
}
string(0) ""
----- Calling 7 -----

Warning: Missing argument 1 for foobar() in %s.php on line %d

Warning: Missing argument 2 for foobar() in %s.php on line %d
foobar
Notice: Undefined variable: a in %s on line %d

Notice: Undefined variable: b in %s on line %d
 got  and 

Notice: Undefined variable: b in %s on line %d

Notice: Undefined variable: a in %s on line %d
array(2) {
  ["A"]=>
  string(3) "one"
  ["B"]=>
  string(3) "two"
}
string(0) ""
----- Calling 8 -----

Warning: Missing argument 1 for foobar() in %s.php on line %d

Warning: Missing argument 2 for foobar() in %s.php on line %d
foobar
Notice: Undefined variable: a in %s on line %d

Notice: Undefined variable: b in %s on line %d
 got  and 

Notice: Undefined variable: b in %s on line %d

Notice: Undefined variable: a in %s on line %d
array(0) {
}
string(0) ""
