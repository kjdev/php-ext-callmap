--TEST--
forward_static_call_map() called.
--FILE--
<?php
if (!extension_loaded('callmap')) {
    dl('callmap.' . PHP_SHLIB_SUFFIX);
}

class A
{
    const NAME = 'A';
    public static function test() {
        $args = func_get_args();
        echo static::NAME, " ", join(',', $args), "\n";
    }
    public static function hoge($arg) {
        $args = func_get_args();
        echo static::NAME, " $arg ", join(',', $args), "\n";
    }
}

class B extends A
{
    const NAME = 'B';

    public static function test() {
        echo self::NAME, "\n";
        forward_static_call_map(array('A', 'test'), array('more', 'args'));
        forward_static_call_map('test', array('other', 'args'));
    }

    public static function hoge($arg) {
        echo self::NAME, "\n";
        forward_static_call_map(array('A', 'hoge'),
                                array('arg' => 'more', 'args'));
        forward_static_call_map('hoge',
                                array('arg' => 'other', 'args'));
    }
}

B::test('foo');

function test() {
    $args = func_get_args();
    echo "C ".join(',', $args)."\n";
}

B::hoge('foo');

function hoge($arg) {
    $args = func_get_args();
    echo "C $arg ".join(',', $args)."\n";
}

?>
--EXPECTF--
B
B more,args
C other,args
B
B more more
C other other
