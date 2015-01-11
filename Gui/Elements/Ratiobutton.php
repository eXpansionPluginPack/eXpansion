<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;
use ManiaLive\Gui\ActionHandler;

class Ratiobutton extends \ManiaLivePlugins\eXpansion\Gui\Control {

    protected $label;
    protected $button;
    protected $active = false;
    protected $textWidth;
    protected $action;
    protected $buttonac;

    function __construct($sizeX = 3, $sizeY = 3, $textWidth = 25) {
        $this->textWidth = $textWidth;
        $this->action = $this->createAction(array($this, 'toggleActive'));
        $config = Config::getInstance();

        $this->button = new \ManiaLib\Gui\Elements\Quad(12, 6);
        $this->button->setAlign('center', 'center');
        $this->button->setAction($this->action);
        $this->button->setScriptEvents(true);
        
        $this->addComponent($this->button);


        $this->label = new \ManiaLib\Gui\Elements\Label($textWidth, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setTextSize(1);
        //$this->label->setStyle("TextCardInfoSmall");		                
        $this->addComponent($this->label);
        $this->setSize(10 + $textWidth, 5);
    }

    protected function onResize($oldX, $oldY) {
		parent::onResize($this->textWidth + 10, 5);
		
        $this->button->setSize(10,5);
        $this->button->setPosition(0, -0.5);
        $this->label->setSize($this->textWidth,6);
        $this->label->setPosition(8, 0);
		
    }

    function onDraw() {
        $config = Config::getInstance();

        if ($this->active) {
            $this->button->setImage($config->getImage("ratiobutton","normal_on.png"), true);
        } else {
            $this->button->setImage($config->getImage("ratiobutton","normal_off.png"), true);
        }
    }

    function setStatus($boolean) {
        $this->active = $boolean;
    }

    function getStatus() {
        return $this->active;
    }

    function getText() {
        return $this->label->getText();
    }

    function setText($text) {
        $this->label->setText('$fff' . $text);
    }

    function toggleActive($login) {
        $this->active = !$this->active;
        $this->redraw();
    }

    function setAction($action) {
        $this->button->setAction($action);
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target) {
        parent::onIsRemoved($target);
        parent::destroy();
    }
}

?>