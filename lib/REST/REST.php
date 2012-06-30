<?php

require dirname(__FILE__) . '/modules/character.php';
require dirname(__FILE__) . '/modules/item.php';
require dirname(__FILE__) . '/exception/REST_exception.php';

class REST {

    protected $_options;
    protected $_ssl = false;

    public function __construct() {
        $this->paramsSet();
    }

    private function paramsSet() {
        if (!isset($this->_options['region'])) {
            $this->_options['region'] = 'eu';
        }
        if (!isset($this->_options['sslSupport'])) {
            $this->_options['sslSupport'] = false;
        }
    }

    private function authenticateUser() {
        
    }

    private function getData() {
        
    }

    public function initREST() {
        
    }

    private function generateUrl($param, $realm, $name) {
        $generatedUrl = (($this->_options['sslSupport']) ? 'https' : 'http') . '://' . $this->_options['region'] . '.battle.net/api/wow/' . $param . '/' . $realm . '/' . $name;
        return $generatedUrl;
    }

    public function __set($key, $value) {
        $this->_options[$key] = $value;
    }

    public function __get($key) {

        if ($key === "character") {
            if (preg_match("/FIELDS/i", $this->_options[$key])) {
                $completeParamList = explode(' FIELDS ', $this->_options[$key]);
                $fields = $completeParamList[1];
                $this->_options['setCharacter'] = $completeParamList[0];
                unset($completeParamList);
            } else {
                $this->_options['setCharacter'] = $key;
                $fields = null;
            }
            if (preg_match("/IMAGE/i", $this->_options[$key])) {
                $filteredCharacter = explode(" IMAGE ", $this->_options[$key]);
                $baseUrl = $this->generateUrl('character', 'Blackrock', $filteredCharacter[0]);
                unset($filteredCharacter);
                return Character::getCharacterImage($baseUrl, $this->_options['region']);
                
            }
            $baseUrl = $this->generateUrl('character', 'Blackrock', $this->_options['setCharacter']);
            return Character::getCharacter($baseUrl, $fields);
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
$cla->character = 'mosny IMAGE';
echo '<pre>';
print_r('<img src="'.$cla->character.'" />');
echo '</pre>';