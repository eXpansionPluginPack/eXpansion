<?php

namespace ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Gui\Controls;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLive\Gui\Container;
use ManiaLivePlugins\eXpansion\Gui\Config;
use ManiaLivePlugins\eXpansion\Gui\Control;
use ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Structures\Checkpoint;

class CheckpointElem extends Control
{

    protected $bg;

    protected $label;

    protected $nick;

    protected $time;

    function __construct($x, Checkpoint $cp = null)
    {
        $sizeX = 35;
        $sizeY = 5;

        $config = Config::getInstance();
        $this->bg = new Quad($sizeX, $sizeY);
        $this->bg->setPosX(-2);
        $this->bg->setId("Bg" . $x);
        $this->bg->setStyle("BgsPlayerCard");
        $this->bg->setSubStyle("BgRacePlayerName");
        $this->bg->setAlign('left', 'center');
        $this->bg->setColorize($config->style_widget_bgColorize); // tämä
        $this->bg->setHidden(1);
        $this->addComponent($this->bg);

        $this->label = new Label(3, 4);
        $this->label->setAlign('right', 'center');
        $this->label->setTextSize(1);
        $this->label->setId("CpPos" . $x);
        $this->label->setPosX(2);
        $this->label->setTextColor($this->getColor("#rank#"));
        $this->addComponent($this->label);

        $this->label = new Label(9, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setTextSize(1);
        $this->label->setId("CpTime" . $x);
        $this->label->setPosX(2.5);
        $this->label->setTextColor($this->getColor("#time#"));
        $this->addComponent($this->label);


        $this->nick = new Label(21, 4);
        $this->nick->setAlign('left', 'center');
        $this->nick->setTextSize(1);
        $this->nick->setPosX(12);
        $this->nick->setId("CpNick_" . $x);
        $this->addComponent($this->nick);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }

    public function onIsRemoved(Container $target)
    {
        parent::onIsRemoved($target);
        $this->destroy();
    }

    private function getColor($var)
    {
        $colors = \ManiaLivePlugins\eXpansion\Core\ColorParser::getInstance();

        return str_replace('$', "", $colors->getColor($var));
    }

}