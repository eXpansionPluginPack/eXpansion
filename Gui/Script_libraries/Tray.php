<?php

namespace ManiaLivePlugins\eXpansion\Gui\Script_libraries;

use ManiaLivePlugins\eXpansion\Gui\Structures\Script;

/**
 * Description of ButtonScript
 *
 * @author Reaby
 */
class Tray extends Script {

    function __construct() {
	parent::__construct("Gui/Scripts/TrayWidget");
	$this->setParam("disableAutoClose", true);
	$this->setParam("autoCloseTimeout", 0);
	$this->setParam('posXMin', 0);
	$this->setParam('posX', 0);
	$this->setParam('posXMax', 29);
	$this->setParam("isMinimized", "False");
    }

    /**
     * Disable autoclose 
     * @param bool $value
     */
    public function setDisableAutoClose($value = true) {
	$this->setParam("disableAutoClose", $value);
    }

    /**
     * autoclose timeout 
     * @param int $value in milliseconds
     */
    public function setAutoCloseTimeOut($value = 0) {
	$this->setParam("autoCloseTimeout", $value);
    }

    public function setPosX($pos) {
	$this->setParam("posX", $pox);
    }

    public function setPosXMin($pos) {
	$this->setParam("posXMin", $pox);
    }

    public function setPosXMax($pos) {
	$this->setParam("posXMax", $pox);
    }

}

?>
