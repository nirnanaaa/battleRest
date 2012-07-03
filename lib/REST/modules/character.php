<?php

class character{
    public static function getCharacter($baseUrl, $fields = null) {
        if ($fields != null) {
            $baseUrl .= '?fields=' . $fields;
        }
        $characterContent = request::curlRequest($baseUrl);
        return json_decode($characterContent);
    }

    public function image($baseUrl, $region) {
        $things = self::getCharacter($baseUrl);
        $imageloc = 'http://' . $region . '.battle.net/static-render/' . $region . '/';
        $imageloc .= $things->thumbnail;
        return $imageloc;
    }

    public function stats($baseUrl) {
        return self::getCharacter($baseUrl, "stats")->stats;
    }

    public function items($baseUrl) {
        return self::getCharacter($baseUrl, "items")->items;
    }

    public function guild($baseUrl) {
        return self::getCharacter($baseUrl, "guild")->guild;
    }

    public function feed($baseUrl) {
        $characterfeed = self::getCharacter($baseUrl, "feed")->feed;
        return $characterfeed;
    }

    public function spec($baseUrl) {
        return self::getCharacter($baseUrl, "talents")->talents;
    }

    public function reputation($baseUrl) {
        return self::getCharacter($baseUrl, "reputation")->reputation;
    }

    public function appearance($baseUrl) {
        return self::getCharacter($baseUrl, "appearance")->appearance;
    }

    public function titles($baseUrl) {
        return self::getCharacter($baseUrl, "titles")->titles;
    }

    public function professions($baseUrl) {
        return self::getCharacter($baseUrl, "professions")->professions;
    }

    public function pvp($baseUrl) {
        return self::getCharacter($baseUrl, "pvp")->pvp;
    }

    public function quests($baseUrl) {
        return self::getCharacter($baseUrl, "quests")->quests;
    }

    public function achievements($baseUrl) {
        return self::getCharacter($baseUrl, "achievements")->achievements;
    }

    public function companions($baseUrl) {
        return self::getCharacter($baseUrl, "companions")->companions;
    }

    public function mounts($baseUrl) {
        return self::getCharacter($baseUrl, "mounts")->mounts;
    }

    public function build($baseUrl, $type) {

        $treearray = self::getCharacter($baseUrl, "talents")->talents[($type == "main") ? 0 : 1]->trees;
        $build = $treearray[0]->total . '/' . $treearray[1]->total . '/' . $treearray[2]->total;
        return $build;
    }

}