<?php

class process {

    public static function processIncomingData($header, $data) {
        if (kernel::Configuration("apcSupport") && !kernel::Configuration("memcachedSupport")) {
            if (function_exists('apc_store')) {
                if (!apc_exists($header . "item!")) {
                    apc_store($header . "item!", $data);
                    return $data;
                } else {
                    return apc_fetch($header . "item!");
                }
            }else{
                kernel::throwException("APC enabled but module not found!");
            }
        } elseif (!kernel::Configuration("apcSupport") && kernel::Configuration("memcachedSupport")) {
            
        }else{
            return $data;
        }
    }

}