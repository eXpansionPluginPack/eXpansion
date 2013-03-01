<?php

namespace ManiaLivePlugins\eXpansion\Helpers;

class ArrayOfObj {

    static function sortAsc(&$array, $prop) {
        usort($array, function($a, $b) use ($prop) {
                    return $a->$prop > $b->$prop ? 1 : -1;
                });
    }

    static function sortDesc(&$array, $prop) {
        usort($array, function($a, $b) use ($prop) {
                    return $a->$prop > $b->$prop ? -1 : 1;
                });
    }

    static function contains(&$array, $prop) {
        foreach ($array as $class) {
            if (property_exists($class, $prop))
                return true;
        }
        return false;
    }

}

?>
