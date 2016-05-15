<?php

namespace ManiaLivePlugins\eXpansion\Menu\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Gui\Config;

class GroupItem extends \ManiaLivePlugins\eXpansion\Gui\Control
{
    protected $frame;

    public function __construct($itemName)
    {
        $this->sizeX = 40;
        $this->sizeY = 5;

        $label = $this->genLabel($itemName);
        $label->setAttribute("class", "group menu item");
        $label->setAttribute("data-label", $itemName);
        $this->addComponent($label);

        $quad = new \ManiaLib\Gui\Elements\Quad(5, 5);
        $quad->setAlign("right", "center");
        $quad->setStyle("Icons64x64_1");
        $quad->setSubstyle("ShowRight2");
        $quad->setPosX(30);
        $this->addComponent($quad);

        $this->frame = new \ManiaLive\Gui\Controls\Frame(30, 0, new \ManiaLib\Gui\Layouts\Column(40, 40));
        $this->frame->setId($itemName);
        $this->addComponent($this->frame);
    }

    public function addItem($itemName, $handle, $plugin)
    {
        /* @var $label \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel */
        $label = $this->genLabel($itemName);
        $label->setAttribute("class", "sub item");
        $label->setAction($this->createAction(array($plugin, "actionHandler"), $handle));
        $this->frame->addComponent($label);
    }

    private function genLabel($itemName)
    {
        /* @var $config \ManiaLivePlugins\eXpansion\Gui\Config */
        $config = \ManiaLivePlugins\eXpansion\Gui\Config::getInstance();

        $label = new \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel(30, 5);

        $label->setText("    ".\eXpGetMessage($itemName, null));
        $label->setTextSize(1);
        $label->setScriptEvents();
        $label->setStyle("Manialink_Body");
        $label->setBgcolor("000");
        $label->setTextColor("fff");
        $label->setAlign("left", "center");
        if (strlen($config->style_widget_bgColorize) == 6) {
            $label->setFocusAreaColor1($config->style_widget_bgColorize."aa");
        } else {
            $label->setFocusAreaColor1($config->style_widget_bgColorize);
        }

        if (strlen($config->style_widget_title_bgColorize) == 6) {
            $label->setFocusAreaColor2($config->style_widget_title_bgColorize."aa");
        } else {
            $label->setFocusAreaColor2($config->style_widget_title_bgColorize);
        }

        return $label;
    }
}
?>

