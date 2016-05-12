<?php

namespace ManiaLivePlugins\eXpansion\Snow;

use ManiaLivePlugins\eXpansion\Snow\Config;

class Snow extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{
    private $wasWarmup = false;

    public function eXpOnReady()
    {
        $this->enableDedicatedEvents();
    }

    public function onBeginMap($map, $warmUp, $matchContinuation)
    {
        Gui\Windows\SnowParticle::EraseAll();
    }

    public function onBeginMatch()
    {
        Gui\Windows\SnowParticle::EraseAll();
    }

    public function onBeginRound()
    {
        $this->wasWarmup = $this->connection->getWarmUp();
    }

    public function onEndMatch($rankings, $winnerTeamOrMap)
    {
        if ($this->wasWarmup) return;
        $window = Gui\Windows\SnowParticle::Create(null);
        $window->show();
    }

    public function eXpOnUnload()
    {
        Gui\Windows\SnowParticle::EraseAll();
        parent::eXpOnUnload();
    }
}

?>
