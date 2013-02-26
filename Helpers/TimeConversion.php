<?php

namespace ManiaLivePlugins\eXpansion\Helpers;

class TimeConversion {

    public static function MStoTM() {
        $timelimit = explode(":", trim($params[0]));
        if (count($timelimit) == 1)
            return intval($timelimit[0] * 1000);
        else
            return intval($timelimit[0] * 60 * 1000) + intval($timelimit[1] * 1000);
    }

}

?>
