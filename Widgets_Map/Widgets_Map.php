<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Map;

use ManiaLivePlugins\eXpansion\Maps\Maps;
use ManiaLivePlugins\eXpansion\Widgets_Map\Gui\Widgets\Map;

class Widgets_Map extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    public function eXpOnLoad()
    {
        $this->enableDedicatedEvents();
    }

    public function eXpOnReady()
    {
        $this->displayWidget();
    }


    public function onBeginMap($map, $warmUp, $matchContinuation)
    {
        $this->displayWidget($map);
    }


    /**
     * displayWidget(string $login)
     *
     * @param string $login
     */
    public function displayWidget()
    {
        $info = Gui\Widgets\Map::Create();
        $info->setSize(60, 15);
        $info->setScale(0.75);
        $map = $this->storage->currentMap;
        if (isset(Maps::$dbMapsByUid[$this->storage->currentMap->uId])) {
            $map = Maps::$dbMapsByUid[$this->storage->currentMap->uId];
        }
        $info->setMap($map);
        $info->show();
    }

    public function eXpOnUnload()
    {
        Map::EraseAll();
    }
}
