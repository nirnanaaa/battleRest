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
 * @package    item
 * @copyright  Copyright (c) 2010-2012 Florian Kasper (http://khnetworks.com)
 * @license    http://www.gnu.org/licenses/     GPL
 * @version    $Id: item.php 2012-07-03 21:58:39GMT-0200 flo $
 */

/**
 * Module for authentication (only load if enabled in config)
 *
 * @category   BattleREST
 * @package    item
 * @copyright  Copyright (c) 2010-2012 Florian Kasper (http://khnetworks.com)
 * @license    http://www.gnu.org/licenses/     GPL
 */
class item {

    /**
     * Returns RAW Item data as Array
     *
     * @param string $baseUrl
     * @return array
     */
    public static function getItem($baseUrl) {
        $characterContent = request::curlRequest($baseUrl);
        return json_decode($characterContent);
    }

}