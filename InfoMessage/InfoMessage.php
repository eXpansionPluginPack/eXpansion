<?php

namespace ManiaLivePlugins\eXpansion\InfoMessage;

use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Helpers\TimeConversion;
use ManiaLivePlugins\eXpansion\InfoMessage\Config;

class InfoMessage extends ExpPlugin
{

    /** @var Config */
    private $config;

    function eXpOnReady()
    {
        $this->enableTickerEvent();
    }

    function onTick()
    {
        $this->config = Config::getInstance();

        if (count($this->config->infoMessages) < 1)
            return;

        $interval = TimeConversion::MStoTM($this->config->infoInterval) / 1000;

        if ((time() % $interval) == 0) {
            $i = rand(0, count($this->config->infoMessages) - 1);
            $this->eXpChatSendServerMessage($this->config->infoMessageColor . $this->config->infoMessages[$i]);
        }
    }

}

?>