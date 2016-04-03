<?php

namespace ManiaLivePlugins\eXpansion\Snow\Gui\Windows;

use ManiaLivePlugins\eXpansion\Snow\Config;

class SnowParticle extends \ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget
{

    private $frame;

    private $config;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->config = Config::getInstance();
        $this->setAlign("center", "bottom");
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->addComponent($this->frame);

        for ($x = 0; $x < $this->config->particleCount; $x++) {
            $quad = new \ManiaLib\Gui\Elements\Quad(4, 4);
            $quad->setPosition(0, 99, 0);
            $quad->setImage($this->config->texture, true);
            $quad->setAlign("center", "center");
            $quad->setId("p" . $x);
            $quad->setAttribute("class", "particle");
            $quad->setScriptEvents();
            $this->frame->addComponent($quad);
        }

        $script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Snow/Gui/Script");
        $script->setParam("particleCount", $this->config->particleCount);
        $this->registerScript($script);
    }

}

?>
