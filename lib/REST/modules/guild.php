<?php

class guild {

    public static function guildGet($baseUrl, $fields = null) {
        if ($fields != null) {
            $baseUrl .= '?fields=' . $fields;
        }
        return json_decode(request::curlRequest($baseUrl));
    }

    public function members($baseUrl) {
        return self::guildGet($baseUrl, "members")->members;
    }

    public function news($baseUrl) {
        return self::guildGet($baseUrl, "news")->news;
    }

    public function achievement($baseUrl) {
        return self::guildGet($baseUrl, "achievement")->achievement;
    }

}