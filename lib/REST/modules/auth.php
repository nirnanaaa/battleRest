<?php

/**
 * REST API Parser
 *
 * LICENSE
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; 
 * either version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License along with this program; if not, see <http://www.gnu.org/licenses/>.
 * 
 *
 * @category   BattleREST
 * @package    auth
 * @copyright  Copyright (c) 2010-2012 Florian Kasper (http://khnetworks.com)
 * @license    http://www.gnu.org/licenses/     GPL
 * @version    $Id: auth.php 2012-07-03 21:58:21GMT-0200 flo $
 */
/**
 * Module for authentication (only load if enabled in config)
 *
 * @category   BattleREST
 * @package    auth
 * @copyright  Copyright (c) 2010-2012 Florian Kasper (http://khnetworks.com)
 * @license    http://www.gnu.org/licenses/     GPL
 */
class auth {

    /**
     * Gernerates the full BNET Header
     *
     *
     * @param string $request
     * @return string
     */
    public static function generateAuthHeader($request) {
        return "Authorization: BNET " . kernel::Configuration("authenticationPublicKey") . ":" . self::signData($request);
    }

    /**
     * Gernerates the signed Data
     *
     * @param string $url
     * @return string
     */
    public static function signData($url) {
        //http://us.battle.net/api/wow/realm/status
        if(preg_match("/https/i", strtolower($url)))$url = substr($url, 21);
        else $url = substr($url, 20);
        
        return base64_encode(hash_hmac('sha1', utf8_encode(kernel::Configuration("authenticationPrivateKey")), self::stringToSign($url)));
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
