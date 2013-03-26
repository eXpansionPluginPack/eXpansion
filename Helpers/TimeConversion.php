<?php

namespace ManiaLivePlugins\eXpansion\Helpers;

class TimeConversion {

    public static function MStoTM($string) {
        $timelimit = explode(":", trim($string));
        if (count($timelimit) == 1)
            return intval($timelimit[0] * 1000);
        else
            return intval($timelimit[0] * 60 * 1000) + intval($timelimit[1] * 1000);
    }

    public static function TMtoMS($time) {
        $time = intval($time);
        return gmdate("i:s", $time/1000);                
    }
    
}

?>
