<?php

/**
 * REST API Parser Class
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
 * @namespace  REST
 * @package    BattleREST
 * @copyright  Copyright (c) 2010-2012 Florian Kasper (http://khnetworks.com)
 * @license    http://www.gnu.org/licenses/     GPL
 * @version    $Id: REST.php 2012-07-03 17:25:24Z flo $
 */
/**
 * Library for Parsing data from Blizzards RESTful API
 *
 * @category   REST
 * @package    BattleRest
 * @copyright  Copyright (c) 2010-2012 Florian Kasper (http://khnetworks.com)
 * @license    http://www.gnu.org/licenses/     GPL
 */

require dirname(__FILE__) . '/exception/REST_exception.php';
require dirname(__FILE__) . '/essentials/kernel.php';

class REST {

    /**
     * Placeholder container for set and get variables
     * @var REST_ARRAY
     */
    protected $_options;

    /**
     * Stores the loaded modules
     * @var REST_MODULES_LOADED
     */
    public $_module;

    /**
     * Constructor
     *
     * calls initRest() to load some basic modules and check the configuration
     *
     * @return void
     */
    public function __construct() {

        $this->initRest();
    }

    /**
     * Called after Constructor
     *
     * Loads needed modules such as the REQUEST module
     *
     * @return void
     */
    public function initREST() {
        $this->registerModule('request');
        $this->registerModule('process');
        if ($this->_options['memCached'] == true) {
            $this->registerModule('memcached');
        }
        if ($this->_options['apcCaching'] == true) {
            $this->registerModule('apc');
        }
        if ($this->_options['sqlCache'] == true) {
            //NYI
        }
        if (kernel::Configuration("authentication") == true) {
            $this->registerModule('auth');
        }
    }

    /**
     * Register Modules
     *
     * Includes Module $module and creates a new Instance of the Class which is
     * placed in that file
     *
     * @param  string $module
     * @return boolean
     */
    private function registerModule($module) {
        if (!file_exists(dirname(__FILE__) . '/modules/' . $module . '.php')) {
            return kernel::throwException("Failed to load module: " . $module);
        } else {
            require_once dirname(__FILE__) . '/modules/' . $module . '.php';
            $this->_module[$module] = new $module;
            return true;
        }
    }



    /**
     * Sets the parameters after calling the getter
     *
     * If Get is called it sets basic parameters
     *
     * @return void
     */
    private function requestParamsSet() {
        if (!isset($this->_options['region'])) {
            $this->_options['region'] = kernel::Configuration("standardRegion");
        }
        if (!isset($this->_options['sslSupport'])) {
            $this->_options['sslSupport'] = kernel::Configuration("sslSupport");
        }
        if (!isset($this->_options['apcCaching'])) {
            $this->_options['apcCaching'] = kernel::Configuration("apcSupport");
        }
        if (!isset($this->_options['softfail'])) {
            $this->_options['softfail'] = kernel::Configuration("softfail");
        }
    }

    /**
     * Explodes RQL String
     *
     * Expects a String which is splitted into smaller parts
     *
     * @param string $query
     * @return string|vector
     */
    private function explodeQuery() {
        return explode(" ", $this->_options['query']);
    }

    /**
     * URL generation
     *
     * Generates an API Url
     * @param int|float|string $param Guild,Character,Item,Status
     * @param string $realm
     * @param string $name
     * @return string generatedUrl
     * @todo Add seperation for item and realmstatus
     */
    private function generateUrl($param, $realm, $name) {
        $generatedUrl = (($this->_options['sslSupport']) ? 'https' : 'http') . '://' . $this->_options['region'] . '.battle.net/api/wow/' . $param . '/' . $realm . '/' . $name;
        return $generatedUrl;
    }

    /**
     * Performs an CharacterAPI request
     *
     * Calling the named functions if is set in QUERY String
     *
     * @param string $query
     * @return string
     * @todo return states and caching seperation
     */
    private function localCharAction() {
        $this->registerModule('character');

        $subquery = $this->explodeQuery();
        $this->_options['setCharacterName'] = $subquery[1];
        $useableObjects = array('fields', 'image', 'stats', 'spec', 'build', 'guild', 'feed', 'spec', 'reputation', 'appearance', 'titles', 'professions', 'pvp', 'quests', 'achievement', 'companions', 'mounts', 'build');
        foreach ($useableObjects as $fields) {
            if (preg_match('/' . $fields . '/i', strtolower($subquery[4]))) {

                if ($this->_apcCaching) {
                    $URL = $this->generateUrl('character', $subquery[3], $this->_options['setCharacterName']);
                    if (apc_exists($URL)) {
                        $returnString = apc_fetch($URL);
                        return $returnString;
                    } else {
                        $returnString = $this->_module['character']->$fields($URL, ($fields == 'image') ? $this->_options['region'] : (($fields == "build") ? $subquery[5] : null));
                        apc_store($URL, $returnString);
                        return $returnString;
                    }
                } else {
                    return $this->_module['character']->$fields($this->generateUrl('character', $subquery[3], $this->_options['setCharacterName']), ($fields == 'image') ? $this->_options['region'] : (($fields == "build") ? $subquery[5] : null));
                }
            }
        }
    }

    private function itemAction() {
        $this->registerModule('item');
        $subquery = $this->explodeQuery();
        return $this->_module['process']->processIncomingData($subquery[1],$this->_module['item']->getItem((($this->_options['sslSupport']) ? 'https' : 'http') . '://' . $this->_options['region'] . ".battle.net/api/wow/item/" . $subquery[1]));
        
        if ($this->_apcCaching) {
            if (apc_exists($subquery[1] . "item")) {
                $returnString = apc_fetch($subquery[1] . "item");
                return $returnString;
            } else {
                $returnString = $this->_module['item']->getItem((($this->_options['sslSupport']) ? 'https' : 'http') . '://' . $this->_options['region'] . ".battle.net/api/wow/item/" . $subquery[1]);
                apc_store($subquery[1] . "item", $returnString, 600);
                return $returnString;
            }
        } else {
            return $this->_module['item']->getItem((($this->_options['sslSupport']) ? 'https' : 'http') . '://' . $this->_options['region'] . ".battle.net/api/wow/item/" . $subquery[1]);
        }
    }

    private function achievementAction() {
        
    }

    private function guildAction() {
        
    }

    private function realmAction($query) {
        $this->registerModule('realm');
        $subquery = explode(" ", $query);
        if ($this->_apcCaching) {
            if (apc_exists($subquery[1] . "status")) {
                $returnString = apc_fetch($subquery[1] . "status");
                return $returnString;
            } else {
                $returnString = $this->_module['realm']->status($this->_module, $this->_options['region'], $subquery[1]);
                apc_store($subquery[1] . "status", $returnString, 600);
                return $returnString;
            }
        } else {
            return $this->_module['realm']->status($this->_module, $this->_options['region'], $subquery[1]);
        }
    }

    private function queryAction() {
        $subquery = $this->explodeQuery();
        if (preg_match("/character/i", strtolower($subquery[0]))) {
            $this->_options['realm'] = $subquery[3];
            return $this->localCharAction();
        } else if (preg_match("/guild/i", strtolower($subquery[0]))) {
            return $this->guildAction();
        } else if (preg_match("/item/i", strtolower($subquery[0]))) {
            return $this->itemAction();
        } else if (preg_match("/server/i", strtolower($subquery[0]))) {
            return $this->realmAction();
        }
    }

    public function __set($key, $value) {
        $this->_options[$key] = $value;
    }

    public function __get($key) {
        $this->requestParamsSet();
        if ($key === "query") {
            return $this->queryAction();
        }

        if (isset($this->_options[$key])) {
            return $this->_options[$key];
        }
        return null;
    }

    public function __isset($key) {
        return(isset($this->_options[$key]));
    }

}

$cla = new REST();
$cla->query = 'ITEM 50000';
echo '<pre>';
print_r($cla->query);
echo '</pre>';