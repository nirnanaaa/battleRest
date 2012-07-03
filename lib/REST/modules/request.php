<?php

class request {

    public function __construct() {
        
    }

    public static function curlRequest($url) {

        if (!function_exists('curl_init')) {
            return file_get_contents($url);
        } else {
print_r(auth::generateAuthHeader($url));
            $errno = CURLE_OK;
            $error = '';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            if (kernel::Configuration("authentication")) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(auth::generateAuthHeader($url)));
            }
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_VERBOSE, false);
            curl_setopt($ch, CURLOPT_URL, $url);


// Execute
            $response = curl_exec($ch);
//Deal with HTTP errors
            $errno = curl_errno($ch);
            $error = curl_error($ch);

            curl_close($ch);

            if ($errno) {
                return false;
            } else {
                return $response;
            }
        }
    }

}