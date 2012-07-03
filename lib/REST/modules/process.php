<?php

class process {

    public static function support() {
        if (kernel::Configuration("apcSupport") && !kernel::Configuration("memcachedSupport")) {
            return "apc";
        } elseif (!kernel::Configuration("apcSupport") && kernel::Configuration("memcachedSupport")) {
            return "memcached";
        }
    }

    public static function apcSupport() {
        return (function_exists('apc_store') ? true : false);
    }

    public static function memcachedSupport() {
        return (function_exists('memcache_add') ? true : false);
    }

    public static function store($var, $value) {
        if (self::support() == "apc") {
            if (self::apcSupport()) {
                apc_store($var, $value, kernel::Configuration("cacheSaveTime"));
                return apc_fetch($var);
            } else {
                kernel::throwException("APC enabled but not available");
                return false;
            }
        } elseif (self::support() == "memcached") {
            if (self::memcachedSupport) {
                memcached::set($var, $value);
                return memcached::get($var);
            } else {
                kernel::throwException("Memcached enabled but not available");
                return false;
            }
        }
    }

    public static function check($var) {
        if (self::support() == "apc") {
            if (self::apcSupport()) {
                if (apc_exists($var)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                kernel::throwException("APC enabled but not available");
                return false;
            }
        }
    }

    public static function fetch($var) {
        if (self::support() == "apc") {
            if (self::apcSupport()) {
                return apc_fetch($var);
            } else {
                kernel::throwException("APC enabled but not available");
                return false;
            }
        } elseif (self::support() == "memcached") {
            if (self::memcachedSupport) {
                return memcached::get($var);
            } else {
                kernel::throwException("Memcached enabled but not available");
                return false;
            }
        }
    }

}