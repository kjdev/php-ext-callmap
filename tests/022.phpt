--TEST--
call_user_func_array()/forward_static_call_array() called.
--FILE--
<?php
if (!extension_loaded('callmap')) {
    dl('callmap.' . PHP_SHLIB_SUFFIX);
}

function test($a, $b) {
    echo __FUNCTION__, " got $a and $b\n";
    return $a . $b;
}

class Test
{
    static public function foo($a, $b) {
        echo __METHOD__, " got $a and $b\n";
        return $a . $b;
    }
}

function _test($params) {
    var_dump($params);
    $ret = call_user_func_array('test', $params);
    var_dump('call_user_func_array:' . $ret);
    $ret = call_user_func_map('test', $params);
    var_dump('call_user_func_map:' . $ret);
    echo "\n";
}

function _test_static($params) {
    var_dump($params);
    $ret = forward_static_call_array(array('Test', 'foo'), $params);
    var_dump('forward_static_call_array:' . $ret);
    $ret = forward_static_call_map(array('Test', 'foo'), $params);
    var_dump('forward_static_call_map:' . $ret);
    echo "\n";
}

_test(array('1', '2'));
_test(array('a' => '1', 'b' => '2'));
_test(array('b' => '2', 'a' => '1'));
_test(array('2', 'a' => '1'));
_test(array('b' => '2', '1'));

_test_static(array('1', '2'));
_test_static(array('a' => '1', 'b' => '2'));
_test_static(array('b' => '2', 'a' => '1'));
_test_static(array('2', 'a' => '1'));
_test_static(array('b' => '2', '1'));
?>
DONE
--EXPECTF--
array(2) {
  [0]=>
  string(1) "1"
  [1]=>
  string(1) "2"
}
test got 1 and 2
string(23) "call_user_func_array:12"
test got 1 and 2
string(21) "call_user_func_map:12"

array(2) {
  ["a"]=>
  string(1) "1"
  ["b"]=>
  string(1) "2"
}
test got 1 and 2
string(23) "call_user_func_array:12"
test got 1 and 2
string(21) "call_user_func_map:12"

array(2) {
  ["b"]=>
  string(1) "2"
  ["a"]=>
  string(1) "1"
}
test got 2 and 1
string(23) "call_user_func_array:21"
test got 1 and 2
string(21) "call_user_func_map:12"

array(2) {
  [0]=>
  string(1) "2"
  ["a"]=>
  string(1) "1"
}
test got 2 and 1
string(23) "call_user_func_array:21"
test got 1 and 2
string(21) "call_user_func_map:12"

array(2) {
  ["b"]=>
  string(1) "2"
  [0]=>
  string(1) "1"
}
test got 2 and 1
string(23) "call_user_func_array:21"
test got 1 and 2
string(21) "call_user_func_map:12"

array(2) {
  [0]=>
  string(1) "1"
  [1]=>
  string(1) "2"
}
Test::foo got 1 and 2
string(28) "forward_static_call_array:12"
Test::foo got 1 and 2
string(26) "forward_static_call_map:12"

array(2) {
  ["a"]=>
  string(1) "1"
  ["b"]=>
  string(1) "2"
}
Test::foo got 1 and 2
string(28) "forward_static_call_array:12"
Test::foo got 1 and 2
string(26) "forward_static_call_map:12"

array(2) {
  ["b"]=>
  string(1) "2"
  ["a"]=>
  string(1) "1"
}
Test::foo got 2 and 1
string(28) "forward_static_call_array:21"
Test::foo got 1 and 2
string(26) "forward_static_call_map:12"

array(2) {
  [0]=>
  string(1) "2"
  ["a"]=>
  string(1) "1"
}
Test::foo got 2 and 1
string(28) "forward_static_call_array:21"
Test::foo got 1 and 2
string(26) "forward_static_call_map:12"

array(2) {
  ["b"]=>
  string(1) "2"
  [0]=>
  string(1) "1"
}
Test::foo got 2 and 1
string(28) "forward_static_call_array:21"
Test::foo got 1 and 2
string(26) "forward_static_call_map:12"

DONE
