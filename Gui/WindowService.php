<?php

namespace ManiaLivePlugins\eXpansion\Gui;

class WindowService {

    private static $positions = array();

    static function setPosition($login, $id, $pos) {
        self::$positions[$id][$login] = $pos;        
    }

    static function getPosition($login, $id) {                
        if (array_key_exists($id, self::$positions))
            if (array_key_exists($login, self::$positions[$id]))
                return self::$positions[$id][$login];
        return "0,0";
    }

}

?>
