<?php

class Item {

    public static function getItem($baseUrl) {
        $characterContent = @file_get_contents($baseUrl);
        return json_decode($characterContent);
    }

}