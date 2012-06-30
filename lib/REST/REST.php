<?php

require dirname(__FILE__) . '/exception/REST_exception.php';

class REST {

    protected $_options;
    protected $_region = "eu"; //$this->_options['region'] will override this!
    protected $_sslSupport = true;
    protected $_apcCaching = false;
    protected $_authentication = false;
    protected $_authtoken = "";
    protected $_sqlCaching = false; //NYI

    /* protected $_memCaching = false;
      protected $_memCachedServer = "";
      protected $_memCachedPort = "";
     */

    public function __construct() {
        $this->paramsSet();
        $this->initRest();
    }

    private function paramsSet() {
        if (!isset($this->_options['region'])) {
            $this->_options['region'] = $this->_region;
        }
        if (!isset($this->_options['sslSupport'])) {
            $this->_options['sslSupport'] = $this->_sslSupport;
        }
        if (!isset($this->_options['apcCaching'])) {
            $this->_options['apcCaching'] = $this->_apcCaching;
        }
    }

    public function initREST() {
        $this->registerModule('character');
        $this->registerModule('item');
        if ($this->_options['apcCaching'] == true) {
            $this->registerModule('apc');
        }
        if ($this->_options['memCached'] == true) {
            $this->registerModule('memcached');
        }
        if ($this->_options['sqlCache'] == true) {
            //NYI
        }
        if ($this->_authentication == true) {
            $this->registerModule('auth');
        }
    }

    private function registerModule($module) {
        if (!file_exists(dirname(__FILE__) . '/modules/' . $module . '.php')) {
            throw new BattleRestException("Failed to load Module " . $module);
        } else {
            require_once dirname(__FILE__) . '/modules/' . $module . '.php';
        }
    }

    private function generateUrl($param, $realm, $name) {
        $generatedUrl = (($this->_options['sslSupport']) ? 'https' : 'http') . '://' . $this->_options['region'] . '.battle.net/api/wow/' . $param . '/' . $realm . '/' . $name;
        return $generatedUrl;
    }

    private function localCharAction($query) {

        $subquery = explode(" ", $query);
        $this->_options['setCharacterName'] = $subquery[1];
        if (preg_match("/fields/i", strtolower($subquery[4]))) {
            return Character::getCharacter($this->generateUrl('character', $subquery[3], $this->_options['setCharacterName']), $subquery[5]);
        } elseif (preg_match("/image/i", strtolower($subquery[4]))) {
            return Character::getCharacterImage($this->generateUrl('character', $subquery[3], $this->_options['setCharacterName']), $this->_options['region']);
        } elseif (preg_match("/stats/i", strtolower($subquery[4]))) {
            return Character::getCharacterStats($this->generateUrl('character', $subquery[3], $this->_options['setCharacterName']));
        } elseif (preg_match("/spec/i", strtolower($subquery[4]))) {
            return Character::getCharacterSpec($this->generateUrl('character', $subquery[3], $this->_options['setCharacterName']));
        } elseif (preg_match("/build/i", strtolower($subquery[4]))) {
            return Character::getCharacterBuild($this->generateUrl('character', $subquery[3], $this->_options['setCharacterName']), $subquery[5]);
        }
        $this->_options['setCharacterFields'] = $subquery[1];
        return $this->characterAction($subquery[1]); //character, fields or image
    }

    private function characterAction($character) {
        if (preg_match("/FIELDS/i", $character)) {
            $completeParamList = explode(' FIELDS ', $character);
            $fields = $completeParamList[1];
            $this->_options['setCharacter'] = $completeParamList[0];
            unset($completeParamList);
        } else {
            $this->_options['setCharacter'] = $key;
            $fields = null;
        }
        if (preg_match("/IMAGE/i", $character)) {
            
        }
        if (preg_match("/IMAGE/i", $character)) {
            $filteredCharacter = explode(" IMAGE ", $character);
            $baseUrl = $this->generateUrl('character', 'Blackrock', $filteredCharacter[0]);
            unset($filteredCharacter);
            return Character::getCharacterImage($baseUrl, $this->_options['region']);
        }
        $baseUrl = $this->generateUrl('character', 'Blackrock', $this->_options['setCharacter']);
        return Character::getCharacter($baseUrl, $fields);
    }

    private function itemAction() {
        
    }

    private function achievementAction() {
        
    }

    private function guildAction() {
        
    }

    private function queryAction($query) {
        $subquery = explode(" ", $query);
        $this->_options['realm'] = $subquery[3];

        if (preg_match("/character/i", strtolower($subquery[0]))) { //CHARACTER or character or Character wayne:D
            return $this->localCharAction($query);
        }
    }

    public function __set($key, $value) {
        $this->_options[$key] = $value;
    }

    public function __get($key) {
        if ($key === "query") {
            return $this->queryAction($this->_options[$key]);
        }
        if ($key === "character") {
            return $this->characterAction($this->_options[$key]);
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
$cla->realm = 'blackrock';
$cla->query = 'CHARACTER mosny FROM blackrock BUILD main';
echo '<pre>';
print_r($cla->query);
//print_r('<img src="' . $cla->character . '" />');
echo '</pre>';