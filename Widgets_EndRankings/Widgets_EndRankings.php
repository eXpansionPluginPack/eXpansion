<?php

namespace ManiaLivePlugins\eXpansion\Widgets_EndRankings;

class Widgets_EndRankings extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    function exp_onInit() {
        //Important for all eXpansion plugins.
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_ROUNDS);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TIMEATTACK);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TEAM);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_LAPS);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_CUP);
    }

    function exp_onLoad() {
        $this->enableDedicatedEvents();
    }

    function exp_onReady() {
        // $this->displayWidget();
    }

    /**
     * displayWidget(string $login)
     * @param string $login
     */
    function displayWidget($login = null) {
        $info = Gui\Widgets\RanksPanel::Create(null);
        $info->setData($this->callPublicMethod("eXpansion\LocalRecords", "getRanks"));
        $info->setSize(38, 95);
        $info->setPosition(-160, 60);

        $info->show();
    }

    public function onBeginMatch() {
        Gui\Widgets\RanksPanel::EraseAll();
    }

    public function onEndMatch($rankings, $winnerTeamOrMap) {
        $this->displayWidget();
    }

}
?>

