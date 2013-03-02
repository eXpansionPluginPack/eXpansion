<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\types;

/**
 * Description of Interger
 *
 * @author oliverde8
 */
class Integer extends \ManiaLivePlugins\eXpansion\AdminGroups\types\absChecker {

    private $options = array("flags" => FILTER_NULL_ON_FAILURE);
    private $range = false;
    
    public function check($data) {
        $value = filter_var($data, FILTER_VALIDATE_INT, $this->options);

        if ($value === null)
            return false;
        else
            return $value;
    }

    /**
     * Adds optional range detection 
     * @param int $min
     * @param int $max
     */
    public function addRange($min, $max) {
        $this->range = "$min to $max";
        array_push($this->options, array("options" => array("min_range" => $min, "max_range" => $max)));
    }

    public function getErrorMsg() {
        if ($range) return "A numerical value in range ($range) was expected!";
        return "A numerical value was expected";
    }

}

?>
