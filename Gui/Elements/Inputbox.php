<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;

class Inputbox extends \ManiaLive\Gui\Control {

    private $label;
    private $button;
    private $buttonSize;
    private $name;
    private $border;
    private $mysizeX;

    function __construct($name, $sizeX = 35, $editable = true) {
        $config = Config::getInstance();
        $this->sizeX = $sizeX;
        $this->name = $name;

        $this->createButton($editable);

        $this->label = new \ManiaLib\Gui\Elements\Label(30, 3);
        $this->label->setAlign('left', 'center');
        $this->label->setTextSize(1);	
        $this->label->setStyle("TextValueSmallSm");
	$this->label->setScale(0.9);
	$this->label->setTextEmboss();
        $this->addComponent($this->label);

        $this->border = new \ManiaLib\Gui\Elements\Quad();
        $this->border->setSize($sizeX + 2, 5.5);
        $this->border->setPosX(-1);
        $this->border->setAlign("left", "center");
        $this->border->setStyle("Bgs1InRace");
        $this->border->setSubStyle("BgColorContour");
        $this->addComponent($this->border);


        $this->setSize($sizeX + 2, 12);
    }

    protected function onResize($oldX, $oldY) {
        // parent::onResize($oldX, $oldY);
        $this->button->setSize($this->getSizeX(), 4);
        $this->border->setSize($this->getSizeX() + 2, 6);
        $this->label->setSize($this->getSizeX(), 3);
        $this->label->setPosition(0, 4);
    }

    protected function createButton($editable) {
        $text = "";
        if ($this->button != null) {
            $this->removeComponent($this->button);
            $text = $this->getText();
        }

        if ($editable) {
            $this->button = new \ManiaLib\Gui\Elements\Entry($this->sizeX, 4.5);
            $this->button->setName($this->name);
            $this->button->setId($this->name);
            $this->button->setDefault($text);
            $this->button->setScriptEvents(true);
            $this->button->setStyle("TextValueMedium");
            $this->button->setFocusAreaColor1("0000");
            $this->button->setFocusAreaColor2("2af6");
            $this->button->setTextEmboss();
        } else {
            $this->button = new \ManiaLib\Gui\Elements\Label($this->sizeX, 5);
            $this->button->setText($text);
        }

        $this->button->setAlign('left', 'center');
        $this->button->setTextColor('fff');
        $this->button->setTextSize(1.5);
        $this->addComponent($this->button);
    }

    public function setEditable($state) {
        if ($state && $this->button instanceof \ManiaLib\Gui\Elements\Label) {
            $this->createButton($state);
        } elseif (!$state && $this->button instanceof \ManiaLib\Gui\Elements\Entry) {
            $this->createButton($state);
        }
    }

    function getLabel() {
        return $this->label->getText();
    }

    function setLabel($text) {
        $this->label->setText('$3af' . $text);
    }

    function getText() {
        if ($this->button instanceof \ManiaLib\Gui\Elements\Entry)
            return $this->button->getDefault();
        else
            return $this->button->getText();
    }

    function setText($text) {
        if ($this->button instanceof \ManiaLib\Gui\Elements\Entry)
            $this->button->setDefault($text);
        else
            $this->button->setText($text);
    }

    function getName() {
        return $this->button->getName();
    }

    function setName($text) {
        $this->button->setName($name);
    }

    function setId($id) {
        $this->button->setId($id);
        $this->button->setScriptEvents();
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target) {
        parent::onIsRemoved($target);
        parent::destroy();
    }

}

?>