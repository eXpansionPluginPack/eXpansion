<?php

namespace ManiaLivePlugins\eXpansion\ChatBackground\Gui\Windows;

use ManiaLib\Gui\Elements\Quad;
use ManiaLive\Gui\Window;
use ManiaLivePlugins\eXpansion\ChatBackground\Config;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget;

class BoxWindow extends PlainWidget
{
    protected $quad;
    protected $quad2;
    protected $config;

    protected function onConstruct()
    {
        parent::onConstruct();

        $this->config = Config::getInstance();

        $this->setName("chatBackground");

        $this->quad = new Quad($this->config->width, $this->config->height);
        $this->quad->setStyle("BgsPlayerCard");
        $this->quad->setSubStyle("BgRacePlayerName");
        $this->quad->setPosition(0, $this->config->posY, $this->config->posZ);
        $this->quad->setColorize($this->config->color);
        $this->quad->setOpacity($this->config->opacity);
        $this->quad->setAlign("center", "bottom");
        $this->addComponent($this->quad);

        $this->quad2 = new Quad($this->config->width * 2, $this->config->height);
        $this->quad2->setPosition(0, $this->config->posY, $this->config->posZ);
        $this->quad2->setColorize($this->config->colorHighlite);
        $this->quad2->setOpacity($this->config->opacity);
        $this->quad2->setStyle("BgsPlayerCard");
        $this->quad2->setSubStyle("BgRacePlayerLine");
        $this->quad2->setAttribute("rot", 180);
        $this->quad2->setAlign("center", "top");
        $this->addComponent($this->quad2);

        $this->registerScript(new Script("ChatBackground\Gui\Script"));

    }
}
