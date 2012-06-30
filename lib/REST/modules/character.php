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

    public static function getCharacterFeed($baseUrl, $formatted) {
        $characterfeed = self::getCharacter($baseUrl, "feed")->feed;
        if ($formatted) {
            foreach ($characterfeed as $feed) {
                $formattedString .= date("r", substr($feed->timestamp, 0, strlen($feed->timestamp) - 3));
                if ($feed->type == "BOSSKILL" || $feed->type == "ACHIEVEMENT") {
                    $formattedString .= "  " . $feed->achievement->title;
                } else if ($feed->type == LOOT) {
                    $formattedString .= "  " . $feed->itemId;
                }
                $formattedString .= "\n";
            }
            return $formattedString;
        }
        else
            return $characterfeed;
    }

    public static function getCharacterSpec($baseUrl) {
        return self::getCharacter($baseUrl, "talents")->talents;
    }

    public static function getCharacterReputation($baseUrl) {
        return self::getCharacter($baseUrl, "reputation")->reputation;
    }

    public static function getCharacterAppearance($baseUrl) {
        return self::getCharacter($baseUrl, "appearance")->appearance;
    }

    public static function getCharacterTitles($baseUrl) {
        return self::getCharacter($baseUrl, "titles")->titles;
    }

    public static function getCharacterProfessions($baseUrl) {
        return self::getCharacter($baseUrl, "professions")->professions;
    }

    public static function getCharacterPvp($baseUrl) {
        return self::getCharacter($baseUrl, "pvp")->pvp;
    }

    public static function getCharacterQuests($baseUrl) {
        return self::getCharacter($baseUrl, "quests")->quests;
    }

    public static function getCharacterAchievements($baseUrl) {
        return self::getCharacter($baseUrl, "achievements")->achievements;
    }

    public static function getCharacterCompanions($baseUrl) {
        return self::getCharacter($baseUrl, "companions")->companions;
    }
    public static function getCharacterMounts($baseUrl) {
        return self::getCharacter($baseUrl, "mounts")->mounts;
    }
    public static function getCharacterPets($baseUrl) {
        return self::getCharacter($baseUrl, "pets")->pets;
    }
    public static function getCharacterBuild($baseUrl, $type) {

        $treearray = self::getCharacter($baseUrl, "talents")->talents[($type == "main") ? 0 : 1]->trees;
        $build = $treearray[0]->total . '/' . $treearray[1]->total . '/' . $treearray[2]->total;
        return $build;
    }

}