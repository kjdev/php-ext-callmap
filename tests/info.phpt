--TEST--
phpinfo() displays callmap info
--SKIPIF--
--FILE--
<?php
if (!extension_loaded('callmap')) {
    dl('callmap.' . PHP_SHLIB_SUFFIX);
}

phpinfo();
?>
--EXPECTF--
%a
callmap

Call Map support => enabled
Extension Version => %d.%d.%d
%a
