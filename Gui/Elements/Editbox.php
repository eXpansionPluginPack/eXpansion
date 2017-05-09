<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLib\Gui\Elements\Entry;
use ManiaLib\Gui\Elements\Label;
use ManiaLive\Gui\Container;
use ManiaLivePlugins\eXpansion\Gui\Control;
use ManiaLivePlugins\eXpansion\Gui\Config;

class Editbox extends Control
{

    protected $label;

    protected $button;

    protected $name;

    protected $bgleft;
    protected $bgcenter;
    protected $bgright;

    protected $bg;

    public function __construct($name, $sizeX = 100, $sizeY = 30, $editable = true)
    {

        $config = Config::getInstance();
        $this->name = $name;

        $this->createButton($editable);

        $this->bg = new WidgetBackGround(100, 30);

        $this->label = new Label($sizeX, 4);
        $this->label->setAlign('left', 'top');
        $this->label->setTextSize(1);
        $this->label->setAttribute("textfont","Oswald");
        $this->label->setTextEmboss();
        $this->addComponent($this->label);


        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;

        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY)
    {
        $this->button->setSize($this->getSizeX(), $this->getSizeY() - 5);
        $this->button->setPosition(0, 0);

        $this->label->setSize($this->getSizeX(), 3);
        $this->label->setPosition(1, 5);
        $this->bg->setSize($this->sizeX, $this->sizeY);

        parent::onResize($oldX, $oldY);
    }

    protected function createButton($editable)
    {
        $text = "";
        if ($this->button != null) {
            $this->removeComponent($this->button);
            $text = $this->getText();
        }

        if ($editable) {
            $this->button = new TextEdit($this->name, $this->sizeX, $this->sizeY);
            $this->button->setAttribute("class", "isTabIndex isEditable");
            $this->button->setName($this->name);
            $this->button->setId($this->name);
            $this->button->setText($text);

            $this->button->setScriptEvents(true);
        } else {
            $this->button = new Label($this->sizeX, 5);
            $this->button->setText($text);
            $this->button->setTextColor('fff');
            $this->button->setTextSize(1.5);
        }

        $this->button->setAlign('left', 'top');
        $this->button->setPosX(2);
        $this->addComponent($this->button);
    }

    public function setEditable($state)
    {
        if ($state && $this->button instanceof Label) {
            $this->createButton($state);
        } elseif (!$state && $this->button instanceof Entry) {
            $this->createButton($state);
        }
    }

    public function getLabel()
    {
        return $this->label->getText();
    }

    public function setLabel($text)
    {
        $this->label->setText($text);
    }

    public function getText()
    {
        if ($this->button instanceof Entry) {
            return $this->button->getDefault();
        } else {
            return $this->button->getText();
        }
    }

    public function setText($text)
    {
        if ($this->button instanceof Entry) {
            $this->button->setDefault($text);
        } else {
            $this->button->setText($text);
        }
    }

    public function getName()
    {
        return $this->button->getName();
    }

    public function setName($text)
    {
        $this->button->setName($text);
    }

    public function setId($id)
    {
        $this->button->setId($id);
        $this->button->setScriptEvents();
    }

    public function setClass($class)
    {
        $this->button->setAttribute("class", "isTabIndex isEditable " . $class);
    }

    public function onIsRemoved(Container $target)
    {
        parent::onIsRemoved($target);
        parent::destroy();
    }
}
