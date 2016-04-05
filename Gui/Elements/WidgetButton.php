<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;

class WidgetButton extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    protected $button;

    protected $quad;

    protected $text;

    protected $value;

    protected $isActive = false;

    /**
     * Button
     *
     * @param int  $sizeX = 24
     * @param intt $sizeY = 6
     */
    function __construct($sizeX = 12, $sizeY = 12)
    {
        $this->quad = new WidgetBackGround($sizeX, $sizeY);
        $this->quad->setAlign('center', 'top');
        $this->addComponent($this->quad);

        $this->button = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->button->setAlign('center', 'top');
        $this->button->setBgcolor("0000");
        $this->button->setBgcolorFocus("fff6");
        $this->button->setScriptEvents();
        $this->addComponent($this->button);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY)
    {
        $this->quad->setSize($this->sizeX, $this->sizeY);
        $this->quad->setPosZ($this->posZ - 1);
    }

    function getText()
    {
        return $this->text;
    }

    function setText($text)
    {
        if (is_array($text)) {
            $y = 0.5;
            foreach ($text as $row) {
                $label = new DicoLabel($this->sizeX - 2, 3);
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
            $label = new DicoLabel($this->sizeX - 2, 2);
            $label->setAlign('center', 'center');
            $label->setStyle("TextValueMedium");
            $label->setTextSize(1);
            $label->setText($text);
            $this->addComponent($label);
            $this->text = $text;
        }
    }

    function setManialink($url)
    {
        $this->button->setManialink($url);
    }

    function setActive($bool = true)
    {
        $this->isActive = $bool;
    }

    function getValue()
    {
        return $this->value;
    }

    function setValue($text)
    {
        $this->value = $text;
    }

    function setAction($action)
    {
        $this->button->setAction($action);
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target)
    {
        parent::onIsRemoved($target);
        parent::destroy();
    }

}

?>