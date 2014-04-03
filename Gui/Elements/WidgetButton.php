<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;

class WidgetButton extends \ManiaLive\Gui\Control {

    protected $button;
    protected $quad;
    private $text;
    private $value;
    private $isActive = false;

    /**
     * Button
     * 
     * @param int $sizeX = 24
     * @param intt $sizeY = 6
     */
    function __construct($sizeX = 12, $sizeY = 12) {	
        $this->quad = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->quad->setAlign('center', 'center2');
        $this->quad->setStyle("Bgs1InRace");
        $this->quad->setSubStyle("BgList");
        $this->quad->setPosY(-3);
        $this->addComponent($this->quad);

        $this->button = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->button->setAlign('center', 'center2');
        $this->button->setPosY(-3);
        $this->button->setBgcolor("0000");
        $this->button->setBgcolorFocus("fff6");	
        $this->addComponent($this->button);

        $this->sizeX = $sizeX + 2;
        $this->sizeY = $sizeY + 2;
        $this->setSize($sizeX + 2, $sizeY + 2);
    }

    protected function onResize($oldX, $oldY) {
        $this->quad->setSize($this->sizeX, $this->sizeY);
        $this->quad->setPosZ($this->posZ - 1);
    }

    function getText() {
        return $this->text;
    }

    function setText($text) {
        if (is_array($text)) {
            $y = 0;
            foreach ($text as $row) {
                $label = new \ManiaLib\Gui\Elements\Label($this->sizeX, 3);
                $label->setAlign('center', 'center2');
                //$label->setStyle("TextValueMedium");
                $label->setTextSize(1);
                $label->setPosY(-($y * 3.2));
                $label->setText($row);
                $this->addComponent($label);
                $this->text .= $row . " ";
                $y++;
            }
            $this->text = rtrim($this->text);
        } else {
            $label = new \ManiaLib\Gui\Elements\Label($this->sizeX, 2);
            $label->setAlign('center', 'center2');
            $label->setStyle("TextValueMedium");
            $label->setTextSize(1);
            $label->setText($text);
            $this->addComponent($label);
            $this->text = $text;
        }
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
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target) {
        parent::onIsRemoved($target);
        parent::destroy();
    }

}

?>