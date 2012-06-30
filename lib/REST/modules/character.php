<?php

class Character{
    public static function getCharacter($baseUrl, $fields = null){
        if($fields != null){
            $baseUrl .= '?fields='.$fields;
        }
        $characterContent = @file_get_contents($baseUrl);
        return json_decode($characterContent);
    }
    public static function getCharacterImage($baseUrl, $region){
        $things = self::getCharacter($baseUrl);
        $imageloc = 'http://'.$region.'.battle.net/static-render/'.$region.'/';
        $imageloc .= $things->thumbnail;
        return $imageloc;        
    }
    
    
}