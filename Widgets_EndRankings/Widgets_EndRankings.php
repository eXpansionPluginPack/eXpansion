<?php

namespace ManiaLivePlugins\eXpansion\Widgets_EndRankings;

class Widgets_EndRankings extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $wasWarmup = false;

    function exp_onInit() {
        //Important for all eXpansion plugins.
        $this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_ROUNDS);
        $this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK);
        $this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM);
        $this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS);
        $this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP);
    }

    function exp_onLoad() {
        $this->enableDedicatedEvents();
    }
    
    
    /**
     * displayWidget(string $login)
     * @param string $login
     */
    function displayWidget($login = null) {
        $info = Gui\Widgets\RanksPanel::Create(null);
        $info->setData($this->callPublicMethod("\\ManiaLivePlugins\\eXpansion\\LocalRecords\\LocalRecords", "getRanks"));
        $info->setSize(38, 95);
        $info->setPosition(-160, 60);
        $info->show();
    }

    public function onBeginMatch() {
        Gui\Widgets\RanksPanel::EraseAll();
    }

    public function onBeginRound() {
        $this->wasWarmup = $this->connection->getWarmUp();
    }

    public function onEndMatch($rankings, $winnerTeamOrMap) {
        if ($this->wasWarmup)
            return;
        $this->displayWidget();
    }

}
?>

