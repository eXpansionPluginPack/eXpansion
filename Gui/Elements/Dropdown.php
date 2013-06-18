<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;

class Inputbox extends \ManiaLive\Gui\Control {

    private $label;
    private $button;
    private $buttonSize;

    function __construct($name, $sizeX = 35) {      
        $config = Config::getInstance();
        $this->button = new \ManiaLib\Gui\Elements\Entry($sizeX, 6);
        $this->button->setName($name);
        $this->button->setAlign('left', 'center');
        $this->button->setTextColor('000');
        $this->button->setTextSize(1);
        $this->button->setScriptEvents(true);
        $this->addComponent($this->button);

        $this->label = new \ManiaLib\Gui\Elements\Label(30, 3);
        $this->label->setAlign('left', 'center');
        $this->label->setTextSize(1);
        $this->label->setStyle("TextStaticSmall");
        $this->addComponent($this->label);        
        $this->setSize($sizeX, 10);
    }

    protected function onResize($oldX, $oldY) {
        $this->button->setSize($this->sizeX, 4);
        $this->label->setSize($this->sizeX, 3);
        $this->label->setPosition(0, 4);
    }

    function onDraw() {
        
    }

    function getLabel() {
        return $this->label->getText();
    }

    function setLabel($text) {
        $this->label->setText('$222' . $text);
    }

    // todo: Get the actual right text value of the element
    function getText() {
        return $this->button->getDefault();
    }

    function setText($text) {
        $this->button->setDefault($text);
    }

    function getName() {
        return $this->button->getName();
    }

    function setName($text) {
        $this->button->setName($name);
    }

}

?>