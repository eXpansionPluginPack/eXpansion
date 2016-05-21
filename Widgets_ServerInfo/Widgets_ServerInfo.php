<?php

namespace ManiaLivePlugins\eXpansion\Widgets_ServerInfo;

use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Widgets_ServerInfo\Gui\Widgets\ServerInfo;

class Widgets_ServerInfo extends ExpPlugin
{

    public function eXpOnLoad()
    {
        $this->enableDedicatedEvents();
    }

    public function eXpOnReady()
    {
        $this->displayWidget();
    }

    /**
     * displayWidget()
     */
    protected function displayWidget()
    {
        ServerInfo::EraseAll();
        $info = ServerInfo::Create(null);
        $info->setSize(60, 15);
        $info->setScale(0.75);
        $info->setLadderLimits($this->storage->server->ladderServerLimitMin, $this->storage->server->ladderServerLimitMax);
        $info->show();
    }

    public function onBeginMap($map, $warmUp, $matchContinuation)
    {
        $this->displayWidget();
    }

    public function eXpOnUnload()
    {
        ServerInfo::EraseAll();
    }

}
