<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;

class Button extends \ManiaLive\Gui\Control {

    private $label;
    private $button;
    private $value;
    private $isActive = false;
    private $activeFrame;
    private $color;

    function __construct($sizeX = 24, $sizeY = 6) {
        $config = Config::getInstance();

        $this->activeFrame = new \ManiaLib\Gui\Elements\Quad($sizeX + 1.5, $sizeY + 2.5);
        $this->activeFrame->setPosition(-0.5, 0);
        $this->activeFrame->setAlign('left', 'center');
        $this->activeFrame->setStyle("Icons128x128_Blink");
        $this->activeFrame->setSubStyle("ShareBlink");

        $this->button = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->button->setAlign('left', 'center');
        $this->button->setImage($config->button);
        $this->button->setImageFocus($config->buttonActive);
        $this->button->setScriptEvents(true);


        $this->label = new \ManiaLib\Gui\Elements\Label($sizeX, $sizeY);
        $this->label->setAlign('center', 'center');
        //$this->label->setStyle("TextCardInfoSmall");
        $this->label->setScriptEvents(true);

        $this->color = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->color->setAlign('left', 'center');
        $this->color->setBgcolor('999');


        $this->sizeX = $sizeX+2;
        $this->sizeY = $sizeY+2;
        $this->setSize($sizeX+2, $sizeY+2);
    }

    protected function onResize($oldX, $oldY) {
        $this->button->setSize($this->sizeX-2, $this->sizeY-1);
        $this->color->setSize($this->sizeX-2, $this->sizeY-1);
        $this->label->setSize($this->sizeX-2, $this->sizeY-1);
        $this->label->setPosX(($this->sizeX-2) / 2);
        $this->setScale(0.7);
    }

    function onDraw() {
        $this->clearComponents();
        if ($this->isActive)
            $this->addComponent($this->activeFrame);

        $this->addComponent($this->color);
        $this->addComponent($this->button);
        $this->addComponent($this->label);
    }

    function getText() {
        return $this->label->getText();
    }

    function setText($text) {
        $this->label->setText('$000' . $text);
    }

    function setActive($bool = true) {
        $this->isActive = $bool;
    }

    function getValue() {
        return $this->value;
    }
    /**
     * Colorize the button background     
     * @param string $value 3-digit RGB code
     */
    function colorize($value) {
        $this->color->setBgcolor($value);    
    }

    function setValue($text) {
        $this->value = $text;
    }

    function setAction($action) {
        $this->button->setAction($action);
        $this->label->setAction($action);
    }

}

?>