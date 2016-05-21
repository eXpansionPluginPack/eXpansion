<?php

namespace ManiaLivePlugins\eXpansion\Xmas\Gui\Windows;

use ManiaLivePlugins\eXpansion\Xmas\Config;

class XmasWindow extends \ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget
{

    protected $frame;

    protected $config;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->config = Config::getInstance();
        $this->setAlign("center", "bottom");
        $this->setSize($this->config->width * $this->config->repeat, $this->config->height);
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->addComponent($this->frame);

        for ($x = 0; $x < $this->config->repeat; $x++) {
            $quad = new \ManiaLib\Gui\Elements\Quad($this->config->width, $this->config->height);
            $quad->setPosition($this->config->posX, $this->config->posY, $this->config->posZ);
            $quad->setImage($this->config->texture, true);
            $quad->setAlign("left", "top");
            $quad->setId("q" . $x);
            $quad->setAttribute("class", "lineElement");
            $quad->setScriptEvents();
            $this->frame->addComponent($quad);
        }

        $script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Xmas/Gui/Script");
        $this->registerScript($script);


        $this->setScale($this->config->scale);
    }

}
