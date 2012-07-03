<?php

final class autoload{
    private static $instance;
    private static $modules;
    private $count = 0;

    private function __construct()
    {
    }

    public static function singleton()
    {
        if (!isset(self::$instance)) {
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }
    public function registerModule($module) {
        if (!file_exists(dirname(__FILE__) . '/' . $module . '.php')) {
            throw new Exception("Failed to load Module " . $module);
        } else {
            require_once dirname(__FILE__) . '/' . $module . '.php';
            self::$modules[$module] = new $module;
        }
        return self::$modules[$module];
    }
    public function pubishModules(){
        
    }
    public function increment()
    {
        return $this->count++;
    }
    public function __clone()
    {
        trigger_error('Clonen ist nicht erlaubt.', E_USER_ERROR);
    }

    public function __wakeup()
    {
        trigger_error('Deserialisierung ist nicht erlaubt.', E_USER_ERROR);
    }
}