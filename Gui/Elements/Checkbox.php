<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;

class Checkbox extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    protected $label;

    protected $button;

    protected $active = false;

    protected $textWidth;

    protected $action;

    protected $toToggle = null;

    public function __construct($sizeX = 5, $sizeY = 5, $textWidth = 25, Checkbox $toToggle = null)
    {
        $this->textWidth = $textWidth;
        $this->action = $this->createAction(array($this, 'toggleActive'));
        $this->toToggle = $toToggle;

        $config = Config::getInstance();
        $this->button = new \ManiaLib\Gui\Elements\Label($sizeX, $sizeY);
        $this->button->setAlign('left', 'center2');
        $this->button->setTextSize(2);
        $this->button->setAction($this->action);
        $this->button->setText("юд?");
        //$this->button->setScriptEvents(true);
        $this->addComponent($this->button);

        $this->label = new \ManiaLib\Gui\Elements\Label($textWidth, 6);
        $this->label->setAlign('left', 'center');
        $this->label->setTextSize(1);
        $this->label->setScale(1.1);
        $this->label->setAttribute("textfont","Oswald");
        $this->addComponent($this->label);

        $this->setSize($sizeX + $textWidth, $sizeY);
    }

    public function SetIsWorking($state)
    {
        if ($state) {
            if ($this->button->getAction() == -1) {
                $this->button->setAction($this->action);
            }
        } else {
            $this->button->setAction(-1);
        }
    }

    public function ToogleIsWorking()
    {
        if ($this->button->getAction() == -1) {
            $this->button->setAction($this->action);
        } else {
            $this->button->setAction(-1);
        }
    }

    protected function onResize($oldX, $oldY)
    {
        $this->button->setSize(5, 5);
        $this->button->setPosition(0, 0);
        $this->label->setSize($this->textWidth, 5);
        $this->label->setPosition(5, 0);
        parent::onResize($this->textWidth + 5, 5);
    }

    protected function onDraw()
    {
        $config = Config::getInstance();

        if ($this->button->getAction() == -1) {
            if ($this->active) {
                $this->button->setText("юд?");
            } else {
                $this->button->setText("юдЮ");
            }
        } else {
            if ($this->active) {
                $this->button->setText("юд?");
            } else {
                $this->button->setText("юдЮ");
            }
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
        if ($this->toToggle != null) {
            $this->toToggle->ToogleIsWorking($login);
        }
        $this->redraw();
    }

    public function setAction($action)
    {
        $this->button->setAction($action);
    }

    public function destroy()
    {
        $this->button->setAction($this->action);
        parent::destroy();
    }

    public function onIsRemoved(\ManiaLive\Gui\Container $target)
    {
        parent::onIsRemoved($target);
        $this->destroy();
    }
}
