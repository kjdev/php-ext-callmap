--TEST--
forward_static_call_map() calling outside of the inheritance chain.
--FILE--
<?php
if (!extension_loaded('callmap')) {
    dl('callmap.' . PHP_SHLIB_SUFFIX);
}

class A
{
    const NAME = 'A';
    public static function test() {
        echo static::NAME, "\n";
    }
}

class B extends A
{
    const NAME = 'B';

    public static function test() {
        echo self::NAME, "\n";
        forward_static_call_map(array('parent', 'test'), array());
    }
}

class C
{
    const NAME = 'C';

    public static function test() {
        echo self::NAME, "\n";
        forward_static_call_map(array('B', 'test'), array());
    }
}

A::test();
echo "-\n";
B::test();
echo "-\n";
C::test();

?>
===DONE===
--EXPECTF--
A
-
B
B
-
C
B
B
===DONE===
