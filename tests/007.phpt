--TEST--
exception in call_user_func_mapy()
--FILE--
<?php
if (!extension_loaded('callmap')) {
    dl('callmap.' . PHP_SHLIB_SUFFIX);
}

class test {
     function throwException() { throw new Exception("Hello World!\n");
} }

$array = array(new test(), 'throwException');

try {
     call_user_func_map($array, array(1, 2));
} catch (Exception $e) {
     echo $e->getMessage();
}
?>
--EXPECT--
Hello World!
