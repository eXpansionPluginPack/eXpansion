<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLib\Gui\Elements\Entry;
use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLive\Gui\Container;
use ManiaLivePlugins\eXpansion\Gui\Control;
use ManiaLivePlugins\eXpansion\Gui\Config;

class InputboxMasked extends Control
{
    protected $label;
    protected $button;

    /** @var Button */
    protected $nonHidden;
    protected $name;
    protected $bgleft, $bgcenter, $bgright;

    function __construct($name, $sizeX = 35, $editable = true)
    {
        $config = Config::getInstance();
        $this->name = $name;

        $this->createButton($editable);

        $this->label = new Label(30, 3);
        $this->label->setAlign('left', 'center');
        $this->label->setTextSize(1);
        $this->label->setStyle("TextCardMediumWhite");
        $this->label->setTextEmboss();
        $this->addComponent($this->label);

        $this->bgcenter = new Quad(3, 6);
        $this->bgcenter->setAlign("left", "center");
        $this->bgcenter->setStyle("Bgs1InRace");
        $this->bgcenter->setSubStyle("BgColorContour");
        $this->bgcenter->setColorize("555");
        //  $this->addComponent($this->bgcenter);

        $this->setSize($sizeX, 12);
    }

    protected function onResize($oldX, $oldY)
    {
        $yOffset = -7;

        $this->button->setSize($this->getSizeX() - 2, 5);
        $this->button->setPosition(1, $yOffset);

        $this->bgcenter->setSize($this->getSizeX(), 6);
        $this->bgcenter->setPosition(0, $yOffset);


        $this->label->setSize($this->getSizeX(), 3);
        $this->label->setPosition(1, 0);

        // $this->bg->setSize($this->sizeX, $this->sizeY);

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
            $this->button = new Entry($this->sizeX, 4.5);
            $this->button->setAttribute("class", "isTabIndex isEditable");
            $this->button->setAttribute("textformat", "password");
            $this->button->setName($this->name);
            $this->button->setId($this->name);
            $this->button->setDefault($text);
            $this->button->setScriptEvents(true);
            $this->button->setTextColor("000");
            $this->button->setFocusAreaColor1("222");
            $this->button->setFocusAreaColor2("000");
            $this->button->setTextSize(1);
        } else {
            $this->button = new Label($this->sizeX, 5);
            $this->button->setText($text);
            $this->button->setTextSize(1.5);
        }

        $this->button->setAlign('left', 'center');
        $this->button->setTextColor('fff');

        $this->button->setPosition(2, -7);
        $this->button->setSize($this->getSizeX() - 3, 4);
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

    function setShowClearText()
    {
        if ($this->nonHidden == null) {
            $this->nonHidden = New Button(3, 3);
            $this->nonHidden->setIcon("Icons64x64_1", "ClipPause");
            $this->nonHidden->setPosition(-4, 0);
            $this->nonHidden->setId($this->name . "_1");
            $this->nonHidden->setDescription($this->getText());
            $this->addComponent($this->nonHidden);
        }
    }

    function getLabel()
    {
        return $this->label->getText();
    }

    function setLabel($text)
    {
        $this->label->setText($text);
    }

    function getText()
    {
        if ($this->button instanceof Entry) return $this->button->getDefault();
        else return $this->button->getText();
    }

    function setText($text)
    {
        if ($this->button instanceof Entry) $this->button->setDefault($text);
        else $this->button->setText($text);
    }

    function getName()
    {
        return $this->button->getName();
    }

    function setName($text)
    {
        $this->button->setName($text);
    }

    function setId($id)
    {
        $this->button->setId($id);
        $this->button->setScriptEvents();
    }

    function setClass($class)
    {
        $this->button->setAttribute("class", "isTabIndex isEditable " . $class);
    }

    function onIsRemoved(Container $target)
    {
        parent::onIsRemoved($target);
        parent::destroy();
    }
}

?>