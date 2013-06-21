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

    /**
     * Gets a matching object by searching property by value 
     * @param type $array
     * @param string $prop
     * @param string $value
     * @return false|Object $obj
     */
    static function getObjbyPropValue(&$array, $prop, $value) {
        foreach ($array as $class) {
            if (!property_exists($class, $prop))
                throw new \Exception("Property $prop doesn't exists!");

            if ($class->$prop == $value)
                return $class;
        }
        return false;
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
