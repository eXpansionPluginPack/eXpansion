<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;

class Inputbox extends \ManiaLivePlugins\eXpansion\Gui\Control
{
    protected $label;
    protected $button;
    protected $name;
    protected $bgleft, $bgcenter, $bgright;

    public function __construct($name, $sizeX = 35, $editable = true)
    {
        $config = Config::getInstance();
        $this->name = $name;

        $this->bg = new WidgetBackGround(100, 30);
        //	$this->addComponent($this->bg);

        $this->createButton($editable);

        $this->bgcenter = new \ManiaLib\Gui\Elements\Quad(3, 6);
        $this->bgcenter->setStyle("Bgs1InRace");
        $this->bgcenter->setSubStyle("BgColorContour");
        $this->bgcenter->setAlign("left", "center");
        //$this->bgcenter->setImage($config->getImage("inputbox", "center.png"), true);
        $this->bgcenter->setColorize("555");
        //  $this->addComponent($this->bgcenter);

        $this->label = new \ManiaLib\Gui\Elements\Label(30, 3);
        $this->label->setAlign('left', 'top');
        $this->label->setTextSize(1);
        $this->label->setStyle("SliderVolume");
        $this->label->setTextColor('fff');
        $this->label->setTextEmboss();
        $this->addComponent($this->label);


        $this->setSize($sizeX, 12);
    }

    protected function onDraw()
    {


        $yOffset = 0;

        $this->button->setSize($this->getSizeX() - 2, 5);
        $this->button->setPosition(1, $yOffset);

        $this->bgcenter->setSize($this->getSizeX(), 6);
        $this->bgcenter->setPosition(0, $yOffset);

        $this->label->setSize($this->getSizeX(), 3);
        $this->label->setPosition(1, 5);

        $this->bg->setSize($this->sizeX, $this->sizeY);

        parent::onDraw();
    }

    private function createButton($editable)
    {
        $text = "";
        if ($this->button != null) {
            $this->removeComponent($this->button);
            $text = $this->getText();
        }

        if ($editable) {
            $this->button = new \ManiaLib\Gui\Elements\Entry($this->sizeX, 5);
            $this->button->setAttribute("class", "isTabIndex isEditable");
            $this->button->setAttribute("textformat", "default");
            $this->button->setName($this->name);
            $this->button->setId($this->name);
            $this->button->setDefault($text);
            $this->button->setScriptEvents(true);
            //      $this->button->setStyle("TextValueSmall");
            $this->button->setTextSize(1);
            $this->button->setFocusAreaColor1("222");
            $this->button->setFocusAreaColor2("000");
        } else {
            $this->button = new \ManiaLib\Gui\Elements\Label($this->sizeX, 5);
            $this->button->setText($text);
            $this->button->setTextSize(2);
        }

        $this->button->setAlign('left', 'center');
        $this->button->setTextColor('fff');
        $this->button->setPosition(2, -7);
        $this->button->setSize($this->getSizeX() - 3, 4);
        $this->addComponent($this->button);
    }

    public function setEditable($state)
    {
        if ($state && $this->button instanceof \ManiaLib\Gui\Elements\Label) {
            $this->createButton($state);
        } elseif (!$state && $this->button instanceof \ManiaLib\Gui\Elements\Entry) {
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
        if ($this->button instanceof \ManiaLib\Gui\Elements\Entry) return $this->button->getDefault();
        else return $this->button->getText();
    }

    public function setText($text)
    {
        if ($this->button instanceof \ManiaLib\Gui\Elements\Entry) $this->button->setDefault($text);
        else $this->button->setText($text);
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

    public function onIsRemoved(\ManiaLive\Gui\Container $target)
    {
        parent::onIsRemoved($target);
        parent::destroy();
    }
}
