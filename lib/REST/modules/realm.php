<?php
class realm {    
    public function status($region, $realms) {
        foreach (json_decode(request::curlRequest("http://" . $region . ".battle.net/api/wow/realm/status"))->realms as $realm) {
            if (strtolower($realm->name) == strtolower($realms)) {
                return ($realm->status) ? "online" : "offline";
            }
        }

        return false;
    }

}
