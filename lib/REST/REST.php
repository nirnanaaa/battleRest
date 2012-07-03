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
 * @package    REST
 * @copyright  Copyright (c) 2010-2012 Florian Kasper (http://khnetworks.com)
 * @license    http://www.gnu.org/licenses/     GPL
 * @version    $Id: REST.php 2012-07-03 21:58:21GMT-0200 flo $
 */
/**
 * Library for Parsing data from Blizzards RESTful API
 *
 * @category   BattleREST
 * @package    REST
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

        $this->initBasics();
    }

    /**
     * Called after Constructor
     *
     * Loads needed modules such as the REQUEST module
     *
     * @return void
     */
    private function initBasics() {
        $this->registerModule('request');
        $this->registerModule('process');
    }

    private function dynamicModules() {
        if ($this->_options['memCached'] == true) {
            $this->registerModule('memcached');
        }
        if ($this->_options['memCaching'] == true) {
            $this->registerModule('memcached');
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
        if (!isset($this->_options['memCaching'])) {
            $this->_options['memCaching'] = kernel::Configuration("memcachedSupport");
        }
        if (!isset($this->_options['softfail'])) {
            $this->_options['softfail'] = kernel::Configuration("softfail");
        }
        $this->dynamicModules();
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
    private function character() {
        $this->registerModule('character');
        $subquery = $this->explodeQuery();
        $this->_options['setCharacterName'] = $subquery[1];
        $useableObjects = array('fields', 'image', 'stats', 'spec', 'build', 'guild', 'feed', 'spec', 'reputation', 'appearance', 'titles', 'professions', 'pvp', 'quests', 'achievement', 'companions', 'mounts', 'build');
        foreach ($useableObjects as $fields) {
            if (preg_match('/' . $fields . '/i', strtolower($subquery[4]))) {
                return $this->_module['character']->$fields($this->generateUrl('character', $subquery[3], $this->_options['setCharacterName']), ($fields == 'image') ? $this->_options['region'] : (($fields == "build") ? $subquery[5] : null));
            }
        }
    }

    /**
     * Performs an ItemAPI request
     *
     * Returns the item array
     * @return array
     * @todo ajax tooltip
     */
    private function item() {
        $this->registerModule('item');
        $subquery = $this->explodeQuery();
        return $this->_module['item']->getItem((($this->_options['sslSupport']) ? 'https' : 'http') . '://' . $this->_options['region'] . ".battle.net/api/wow/item/" . $subquery[1]);
    }

    /**
     * Performs an AchievementAPI request
     *
     * Returns achievement Informations
     * @return array
     * @todo ALL| NOT TO MUCH API REQUESTS ( 1700 AV's < 3000 REQUESTS)
     */
    private function achievement() {
        
    }

    /**
     * Performs an GuildAPI request
     *
     * Returns Guild information such as roster etc.
     * @return array
     * @todo links to member? ALL
     */
    private function guild() {
        
    }

    /**
     * Performs an RealmStatusAPI request
     *
     * Online or offline
     * @return array
     * @todo realm informations
     */
    private function realm() {
        $this->registerModule('realm');
        $subquery = $this->explodeQuery();
        return $this->_module['realm']->status($this->_options['region'], $subquery[1]);
    }
    /**
     * Query's the users set
     *
     * @return mixed
     */
    private function queryAction() {
        $subquery = $this->explodeQuery();
        $Actions = array("character", "guild", "item", "realm");
        foreach ($Actions as $singleAction) {
            if (preg_match("/" . $singleAction . "/i", strtolower($subquery[0]))) {
                return $this->$singleAction();
            }
        }
    }

    public function __set($key, $value) {
        $this->_options[$key] = $value;
    }

    public function __get($key) {
        $this->requestParamsSet();
        if ($key === "query") {
            return $this->queryAction();
        }else{
           return null; 
        }
        
    }

    public function __isset($key) {
        return(isset($this->_options[$key]));
    }

}

$cla = new REST();
$cla->query = 'CHARACTER mosny FROM Blackrock IMAGE';
echo '<pre>';
print_r($cla->query);
echo '</pre>';