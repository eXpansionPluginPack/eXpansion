<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\Ants\Gui\Widget;

use ManiaLivePlugins\eXpansion\Ants\Config;

/**
 * Description of SpiderWidget
 *
 * @author Petri Järvisalo <petri.jarvisalo@gmail.com>
 */
class AntsWidget extends \ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget
{

    protected $frame;
    protected $config;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->config = Config::getInstance();

        for ($x = 0; $x < $this->config->spriteCount; $x++) {
            $quad = new \ManiaLib\Gui\Elements\Quad(5, 5);
            $quad->setPosition(500, 99, 0);
            $quad->setImage($this->config->texture, true);
            $quad->setAlign("center", "center");
            $quad->setId("ant" . ($x + 1));
            //$quad->setScriptEvents();
            $this->addComponent($quad);
        }

        $script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Ants/Gui/Script");
        $script->setParam("antCount", $this->config->spriteCount);
        $this->registerScript($script);
    }
}
