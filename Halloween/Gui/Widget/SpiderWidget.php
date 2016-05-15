<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\Halloween\Gui\Widget;

use ManiaLivePlugins\eXpansion\Halloween\Config;

/**
 * Description of SpiderWidget
 *
 * @author Petri Järvisalo <petri.jarvisalo@gmail.com>
 */
class SpiderWidget extends \ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget
{

    protected $frame;
    protected $config;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->config = Config::getInstance();

        for ($x = 0; $x < $this->config->spriteCount; $x++) {
            $quad = new \ManiaLib\Gui\Elements\Quad(0.5, 180);
            $quad->setPosition(500, 99, -1);
            $quad->setBgcolor("222a");
            $quad->setAlign("center", "bottom");
            $quad->setId("rope" . ($x + 1));
            $this->addComponent($quad);

            $quad = new \ManiaLib\Gui\Elements\Quad(20, 20);
            $quad->setPosition(500, 99, 0);
            $quad->setImage($this->config->texture, true);
            $quad->setAlign("center", "top");
            $quad->setId("spider" . ($x + 1));
            $quad->setScriptEvents();
            $this->addComponent($quad);
        }

        $script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Halloween/Gui/Script");
        $script->setParam("spiderCount", $this->config->spriteCount);
        $this->registerScript($script);
    }

}
