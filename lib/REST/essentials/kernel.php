<?php

class kernel {

    public static function Configuration($requested) {
        $config = array(
            /**
             * If this is true then it won't throw an exception
             * @var boolean
             */
            "softfail" => false,
            /**
             * Would you like to authenticate against the API?
             * @var boolean
             */
            "authentication" => false,
            /**
             * If you want enter here your API keys
             * @var string
             */
            "authenticationPrivateKey" => "test",
            "authenticationPublicKey" => "test",
            /**
             * the standard region will be used if region isn't set
             * @var string
             */
            "standardRegion" => "eu",
            /**
             * the standard realm will be used if region isn't set
             * @var string
             */
            "standardRealm" => "Blackrock",
            /**
             * the standard character will be used if region isn't set
             * @var string
             */
            "standardChar" => "Mosny",
            /**
             * Do you want ssl support?
             * @var boolean
             */
            "sslSupport" => true,
            /**
             * Do you want curl support?
             * @var boolean
             */
            "curlSupport" => true,
             /**
             * The Time objects beeing stored
             * @var int
             */
            "cacheSaveTime" => 7200,
            /**
             * Do you want apc support?
             * @var boolean
             */
            "apcSupport" => true,
            
            /**
             * do you want memcached support?
             * @var boolean
             */
            "memcachedSupport" => false,
            /**
             * If Memcached is enabled! Your Memcached server
             * @var string
             */
            "memcachedServer" => "",
            "memcachedPort" => 11211,
            "sqlSupport" => false,
        );
        return $config[$requested];
    }
    /**
     * Throws an Exception 
     *
     * Checks if $_softfail is true otherwise it will throw an Exception
     *
     * @param  string $value
     * @return boolean
     */
    public static function throwException($value) {
        if (self::Configuration("softfail")) {
            return false;
        } else {
            throw new BattleRestException($value);
            return false;
        }
    }
}