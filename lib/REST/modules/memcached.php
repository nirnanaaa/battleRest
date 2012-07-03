<?php

class memcached {

    public static function connect() {
        $mCache = new MemCache();
        $mCache->addServer(new MemCacheServer(kernel::Configuration("memcachedServer"), kernel::Configuration("memcachedPort")));
        return $mCache;
    }

    public static function set($key, $value) {
        self::connect()->set($key, $value, false, kernel::Configuration("cacheSaveTime"));
    }

    public static function get() {
        return self::connect()->get('testkey');
    }

    public static function iset() {
        
    }

}