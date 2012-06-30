<?php

class Character {

    public static function getCharacter($baseUrl, $fields = null) {
        if ($fields != null) {
            $baseUrl .= '?fields=' . $fields;
        }
        $characterContent = @file_get_contents($baseUrl);
        return json_decode($characterContent);
    }

    public static function getCharacterImage($baseUrl, $region) {
        $things = self::getCharacter($baseUrl);
        $imageloc = 'http://' . $region . '.battle.net/static-render/' . $region . '/';
        $imageloc .= $things->thumbnail;
        return $imageloc;
    }

    public static function getCharacterStats($baseUrl) {
        return self::getCharacter($baseUrl, "stats")->stats;
    }

    public static function getCharacterItems($baseUrl) {
        return self::getCharacter($baseUrl, "items")->items;
    }

    public static function getCharacterGuild($baseUrl) {
        return self::getCharacter($baseUrl, "guild")->guild;
    }


    public static function getCharacterSpec($baseUrl) {
        return self::getCharacter($baseUrl, "talents")->talents;
    }

    public static function getCharacterBuild($baseUrl, $type) {

        $treearray = self::getCharacter($baseUrl, "talents")->talents[($type == "main") ? 0 : 1]->trees;
        $build = $treearray[0]->total . '/' . $treearray[1]->total . '/' . $treearray[2]->total;
        return $build;
    }

}