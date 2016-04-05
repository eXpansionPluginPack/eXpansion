<?php

namespace ManiaLivePlugins\eXpansion\Gui\Scripts;

/**
 * Description of ButtonScript
 *
 * @author De Cramer Oliver
 */
class DropDownScript extends \ManiaLivePlugins\eXpansion\Gui\Structures\Script
{

    public static $nb = 0;
    public $dropdownIndex;

    function __construct()
    {
        parent::__construct("Gui/Scripts/DropDown");
    }


    public function getDeclarationScript($win, $component)
    {
        $this->dropdownIndex = self::$nb++;

        return parent::getDeclarationScript($win, $component);
    }

    public function getWhileLoopScript($win, $component)
    {
        return parent::getWhileLoopScript($win, $component);
    }

    public function getlibScript($win, $component)
    {
        return parent::getlibScript($win, $component);
    }

    public function reset()
    {
        self::$nb = 0;
    }

    public function multiply()
    {
        return true;
    }
}

?>
