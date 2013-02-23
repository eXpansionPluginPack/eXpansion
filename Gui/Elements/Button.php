<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;

class Button extends \ManiaLive\Gui\Control {

    private $label;
    private $button;
    private $value;
    private $isActive = false;
    private $activeFrame;

    function __construct($sizeX = 25, $sizeY = 7) {
        $config = Config::getInstance();

        $this->activeFrame = new \ManiaLib\Gui\Elements\Quad($sizeX + 1, $sizeY + 1.5);
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
        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY) {
        $this->button->setSize($this->sizeX, $this->sizeY);

        $this->label->setSize($this->sizeX, $this->sizeY);
        $this->label->setPosX($this->sizeX / 2);
    }

    function onDraw() {
        $this->clearComponents();
        if ($this->isActive) 
            $this->addComponent($this->activeFrame);

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

        function setValue($text) {
            $this->value = $text;
        }

        function setAction($action) {
            $this->button->setAction($action);
            $this->label->setAction($action);
        }

    }

?>