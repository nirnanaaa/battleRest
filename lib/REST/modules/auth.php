<?php

class auth {

    /**
     * Gernerates the full BNET Header
     *
     *
     * @param string $request
     * @return string
     */
    public static function generateAuthHeader($request) {
        return "Authorization: BNET " . kernel::Configuration("authenticationPublicKey") . self::signData($request);
    }

    /**
     * Gernerates the signed Data
     *
     * @param string $url
     * @return string
     */
    public static function signData($url) {
        //http://us.battle.net/api/wow/realm/status
        $url = substr($url, 20);
        return base64_encode(hash_hmac('sha1', kernel::Configuration("authenticationPrivateKey"), self::stringToSign($url)));
    }
    /**
     * Gernerates the GET request with timestamp and path
     *
     * @param string $path
     * @return string
     */
    public static function stringToSign($path) {
        return "GET\n" . date("r", time()) . "\n" . $path;
    }

}
