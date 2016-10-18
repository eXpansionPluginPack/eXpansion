<?php

namespace ManiaLivePlugins\eXpansion\Menu\Gui\Controls;

use ManiaLib\Gui\Elements\Quad;

class GroupItem extends \ManiaLivePlugins\eXpansion\Gui\Control
{
    protected $frame;

    public function __construct($itemName, $id)
    {
        $this->sizeX = 40;
        $this->sizeY = 5;

        $quad2 = new Quad(30, 5);
        $config = \ManiaLivePlugins\eXpansion\Gui\Config::getInstance();
        $quad2->setBgcolor($config->style_widget_bgColorize);
        $quad2->setBgcolorFocus($config->style_widget_title_bgColorize);
        $quad2->setAlign("left", "center");
        $quad2->setScriptEvents();
        $quad2->setId("mQuad_" . $id);
        $quad2->setAttribute("class", "group menu item");
        $quad2->setAttribute("data-label", $itemName);
        $this->addComponent($quad2);

        $label = $this->genLabel($itemName);
        $label->setId("item_" . $id);
        $this->addComponent($label);

        $quad = new \ManiaLib\Gui\Elements\Quad(5, 5);
        $quad->setAlign("right", "center");
        $quad->setStyle("Icons64x64_1");
        $quad->setSubstyle("ShowRight2");
        $quad->setPosX(30);
        $quad->setId("quad_" . $id);
        $this->addComponent($quad);

        $this->frame = new \ManiaLive\Gui\Controls\Frame(30, 0, new \ManiaLib\Gui\Layouts\Column(40, 40));
        $this->frame->setId($itemName);
        $this->addComponent($this->frame);
    }

    public function addItem($itemName, $handle, $plugin)
    {

        $frame = new \ManiaLive\Gui\Controls\Frame();
        $frame->setSize(30, 5);

        /* @var $label \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel */
        $label = $this->genLabel($itemName);
        $label->setAttribute("class", "sub item");

        $quad = new Quad(30, 5);
        $config = \ManiaLivePlugins\eXpansion\Gui\Config::getInstance();
        $quad->setBgcolor($config->style_widget_bgColorize);
        $quad->setBgcolorFocus($config->style_widget_title_bgColorize);
        $quad->setAlign("left", "center");
        $quad->setAttribute("class", "sub item");
        $quad->setOpacity(0.75);
        $quad->setScriptEvents();

        if ($handle) {
            $action = $this->createAction(array($plugin, "actionHandler"), $handle);
            //$label->setAction($action);
            $quad->setAction($action);
        }

        $frame->addComponent($quad);
        $frame->addComponent($label);

        $this->frame->addComponent($frame);
    }

    private function genLabel($itemName)
    {
        $label = new \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel(30, 5);
        $label->setText(eXpGetMessage($itemName));
        $label->setTextSize(1);
        $label->setStyle("TextRaceChat");
        $label->setPosX(2);
        $label->setTextColor("fff");
        $label->setAlign("left", "center");

        return $label;
    }
}
