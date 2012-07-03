<?php


class item {

    public static function getItem($baseUrl) {
        $characterContent = request::curlRequest($baseUrl);
        return json_decode($characterContent);
    }

}