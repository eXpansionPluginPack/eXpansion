<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\types;

/**
 * Description of Interger
 *
 * @author oliverde8
 */
class Arraylist extends \ManiaLivePlugins\eXpansion\AdminGroups\types\absChecker
{

    private $haystack = array();

    public function check($data)
    {
        return in_array($data, $this->haystack);
    }

    public function items($data)
    {
        if (is_array($data))
            $this->haystack = $data;
        else
            $this->haystack = explode(",", $data);

        return $this;
    }

    public function getErrorMsg()
    {
        return "A value of following (" . implode(",", $this->haystack) . ") was expected.";
    }

}

?>
