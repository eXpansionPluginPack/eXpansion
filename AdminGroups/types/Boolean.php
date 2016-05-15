<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\types;

/**
 * Description of Interger
 *
 * @author oliverde8
 */
class Boolean extends \ManiaLivePlugins\eXpansion\AdminGroups\types\absChecker
{

    public function check($data) {
        $value = filter_var($data, FILTER_VALIDATE_BOOLEAN | FILTER_NULL_ON_FAILURE);
        if ($value === null) {
            return false;
        }
        else {
            return $value;
        }
    }

    public function getErrorMsg() {
        return "A boolean value or one of following (on, off, yes, no) was expected.";
    }

}
