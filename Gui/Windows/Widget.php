<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Config;

/**
 * @abstract
 */
class Widget extends EmptyWidget {

    private $_name = "widget";
    private $move;
    private $axisDisabled = "";
	
	private $script;

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

        $this->xml = new \ManiaLive\Gui\Elements\Xml();
    }
   

    private function getNumber($number) {
        return number_format((float) $number, 2, '.', '');
    }

    protected function onDraw() {
        $this->script->setParam("name", $this->_name);
        $this->script->setParam("axisDisabled", $this->axisDisabled);
        $this->script->setParam("version", \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION);
		
        $reset = "False";
        if (DEBUG)
            $reset = "True";
        $this->script->setParam("forceReset", $reset);
		
        parent::onDraw();
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

    function setDisableAxis($axis) {
        $this->axisDisabled = $axis;
    }

}

?>
