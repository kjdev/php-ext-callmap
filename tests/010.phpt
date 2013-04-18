--TEST--
forward_static_call_map() called from outside of a method.
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

    public static function test2() {
        echo self::NAME, "\n";
        forward_static_call_map(array('self', 'test'), array());
    }

    public static function test3() {
        echo self::NAME, "\n";
        forward_static_call_map(array('A', 'test'), array());
    }
}

class C extends B
{
    const NAME = 'C';

    public static function test()
    {
        echo self::NAME, "\n";
        forward_static_call_map(array('A', 'test'), array());
    }
}

A::test();
echo "-\n";
B::test();
echo "-\n";
B::test2();
echo "-\n";
B::test3();
echo "-\n";
C::test();
echo "-\n";
C::test2();
echo "-\n";
C::test3();

?>
===DONE===
--EXPECTF--
A
-
B
B
-
B
B
B
-
B
B
-
C
C
-
B
B
C
-
B
C
===DONE===
