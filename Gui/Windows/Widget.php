<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Config;

/**
 * @abstract
 */
class Widget extends PlainWidget {

    private $_name = "widget";
    private $move;
    private $axisDisabled = "";
    private $script;

    /** @var Array() */
    private $positions = array();

    /** @var Array() */
    private $widgetVisible = array();

    /** @var \ManiaLive\Data\Storage */
    private $storage;

    protected function onConstruct() {
	parent::onConstruct();
	$this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\Scripts\WidgetScript");
	$this->registerScript($this->script);

	$this->move = new \ManiaLib\Gui\Elements\Quad(45, 7);
	$this->move->setStyle("Icons128x128_Blink");
	$this->move->setSubStyle("ShareBlink");
	$this->move->setScriptEvents();
	$this->move->setId("enableMove");
	$this->addComponent($this->move);
	$this->storage = \ManiaLive\Data\Storage::getInstance();
	$this->xml = new \ManiaLive\Gui\Elements\Xml();
    }

    /**
     * formats number for maniascript 
     * @param numeric $number
     * @return string
     */
    private function getNumber($number) {
	return number_format((float) $number, 2, '.', '');
    }

    private function getBoolean($boolean) {
	if ($boolean)
	    return "True";
	return "False";
    }

    protected function onDraw() {

	$this->script->setParam("name", $this->_name);
	$this->script->setParam("axisDisabled", $this->axisDisabled);
	$this->script->setParam("version", \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION);
	$this->script->setParam("gameMode", $this->storage->gameInfos->gameMode);

	$this->script->setParam("forceReset", $this->getBoolean(DEBUG));

	parent::onDraw();
    }

    public function onResize($oldX, $oldY) {
	parent::onResize($oldX, $oldY);
	$this->move->setSize($this->getSizeX(), $this->getSizeY());
    }

    function setName($text, $parameter = "") {
	$this->_name = $text;
    }

    function closeWindow() {
	$this->erase($this->getRecipient());
    }

    function destroy() {
	$this->clearComponents();
	parent::destroy();
    }

    /**
     * disable moving for certaint axis
     * @param string $axis accepts values: "x" or "y"
     */
    function setDisableAxis($axis) {
	$this->axisDisabled = $axis;
    }

    /**
     * set a custom position for a gamemode
     * @param string $gameMode
     * @param float $posX
     * @param float $posY
     */
    function setPositionForGamemode($gameMode, $posX, $posY) {
	$this->positions[$gameMode] = array($posX, $posY);
    }

    function getWidgetVisible() {
	if (isset($this->widgetVisible[$this->storage->gameInfos->gameMode])) {
	    $value = $this->widgetVisible[$this->storage->gameInfos->gameMode];
	    return $this->getBoolean($value);
	}
	return "True";
    }

    /**
     * Sets visibility of the widget according to gamemode
     * @param string $gameMode
     * @param bool $value
     */
    function setVisibilityForGamemode($gameMode, $value) {
	$this->widgetVisible[$gameMode] = $value;
    }

    function getPosX() {
	if (isset($this->positions[$this->storage->gameInfos->gameMode])) {
	    return $this->positions[$this->storage->gameInfos->gameMode][0];
	}

	return $this->posX;
    }

    function getPosY() {
	if (isset($this->positions[$this->storage->gameInfos->gameMode])) {
	    return $this->positions[$this->storage->gameInfos->gameMode][1];
	}
	return $this->posY;
    }

}

?>
