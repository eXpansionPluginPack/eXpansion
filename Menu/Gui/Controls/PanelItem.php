<?php

namespace ManiaLivePlugins\eXpansion\Menu\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Gui\Config;

class PanelItem extends \ManiaLivePlugins\eXpansion\Gui\Control
{
    /** @var \ManiaLib\Gui\Elements\Quad */
    protected $bg;
    protected $nick;
    protected $label;
    protected $icon = null;
    protected $time;
    protected $frame;

    function __construct($addArrow = false)
    {
        $config = Config::getInstance();

        $sizeX = 29;
        $sizeY = 5.5;
        $this->setAlign("left", "top");

        $this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->bg->setAlign("left", "top");
        $this->bg->setBgcolor($config->style_widget_bgColorize);
        $this->bg->setBgcolorFocus($config->style_widget_title_bgColorize);
        $this->bg->setOpacity(0.75);
        $this->bg->setScriptEvents();
        $this->addComponent($this->bg);

        $this->label = new \ManiaLib\Gui\Elements\Label($sizeX, $sizeY);
        $this->label->setStyle("TextCardScores2");
        $this->label->setTextSize(1);
        $this->label->setPosX(3);
        $this->label->setPosY(-$sizeY / 2);
        $this->label->setAlign("left", "center");
        $this->label->setTextEmboss();
        $this->addComponent($this->label);

        if ($addArrow) {
            $this->icon = new \ManiaLib\Gui\Elements\Quad(5.5, 5.5);
            $this->icon->setStyle("Icons64x64_1");
            $this->icon->setSubStyle("ShowRight2");
            $this->icon->setPosX(25);
            $this->addComponent($this->icon);
        }

        $this->setSize($sizeX, $sizeY);
    }

    function setText($text)
    {
        $this->label->setText($text);
    }

    function setClass($value)
    {
        $this->bg->setAttribute("class", $value);
        $this->label->setAttribute("class", $value."_lbl");
        if ($this->icon !== null) {
            $this->icon->setAttribute("class", $value."_icon");
        }
    }

    function setId($id)
    {
        $this->bg->setId($id);
        if ($this->icon !== null) {
            $this->icon->setId($id."_icon");
        }
        $this->label->setId($id."_lbl");
    }

    function setAction($action)
    {
        $this->bg->setAttribute('data-action', $action);
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target)
    {
        parent::onIsRemoved($target);
        $this->destroy();
    }

    function destroy()
    {
        parent::destroy();
    }

    function setTop()
    {
        // deprecated
    }

    function setBottom()
    {
        // deprecated
    }
}
?>

