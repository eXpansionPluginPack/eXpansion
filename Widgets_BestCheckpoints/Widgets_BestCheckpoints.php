<?php

namespace ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints;

use ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Gui\Widgets\BestCpPanel;
use ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Structures\Checkpoint;

class Widgets_BestCheckpoints extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    public function exp_onReady()
    {
        $this->enableDedicatedEvents();
        $this->displayWidget(null);
    }

    /**
     * displayWidget(string $login)
     *
     * @param string $login
     */
    function displayWidget($login = null)
    {
        $info = BestCpPanel::Create($login);
        $info->setSize(190, 7);
        $info->show();
    }

    public function onBeginMatch()
    {
        $this->displayWidget(null);
    }

    public function onEndMatch($rankings, $winnerTeamOrMap)
    {
        if ($this->storage->gameInfos->gameMode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK || strtolower($this->storage->gameInfos->scriptName)
            == "timeattack.script.txt") {
            BestCpPanel::EraseAll();
        }
    }

    function exp_onUnload()
    {
        BestCpPanel::EraseAll();
        parent::exp_onUnload();
    }
}
?>

