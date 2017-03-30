<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;

class Ratiobutton extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    protected $label;
    protected $button;
    protected $active = false;
    protected $textWidth;
    protected $action;
    protected $buttonac;

    public function __construct($sizeX = 3, $sizeY = 3, $textWidth = 25)
    {
        $this->textWidth = $textWidth;
        $this->action = $this->createAction(array($this, 'toggleActive'));
        $config = Config::getInstance();

        $this->button = new \ManiaLib\Gui\Elements\Label(12, 6);
        $this->button->setAlign('center', 'center');
        $this->button->setAction($this->action);
        $this->button->setTextSize(2);
        //$this->button->setScriptEvents(true);
        $this->addComponent($this->button);


        $this->label = new \ManiaLib\Gui\Elements\Label($textWidth, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setTextSize(1);
        //$this->label->setAttribute("textfont","Oswald");
        $this->addComponent($this->label);
        $this->setSize(10 + $textWidth, 5);
    }

    protected function onResize($oldX, $oldY)
    {
        parent::onResize($this->textWidth + 10, 5);

        $this->button->setSize(5, 5);
        $this->button->setPosition(0, -0.5);
        $this->button->setText('$fffî¤?');
        $this->label->setSize($this->textWidth, 6);
        $this->label->setPosition(4, 0);
    }

    protected function onDraw()
    {
        $config = Config::getInstance();

        if ($this->active) {
            $this->button->setText('$fffî¤ž');
        } else {
            $this->button->setText('$fffî¤?');
        }
    }

    public function setStatus($boolean)
    {
        $this->active = $boolean;
    }

    public function getStatus()
    {
        return $this->active;
    }

    public function getText()
    {
        return $this->label->getText();
    }

    public function setText($text)
    {
        $this->label->setText('$fff' . $text);
    }

    public function toggleActive($login)
    {
        $this->active = !$this->active;
        $this->redraw();
    }

    public function setAction($action)
    {
        $this->button->setAction($action);
    }

    public function onIsRemoved(\ManiaLive\Gui\Container $target)
    {
        parent::onIsRemoved($target);
        parent::destroy();
    }
}
