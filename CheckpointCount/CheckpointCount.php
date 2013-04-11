<?php

namespace ManiaLivePlugins\eXpansion\CheckpointCount;

use \ManiaLivePlugins\eXpansion\CheckpointCount\Gui\Widgets\CPPanel;

class CheckpointCount extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

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

    public function exp_onReady() {
        foreach ($this->storage->players as $player)
            $this->onPlayerConnect($player->login, false);
        foreach ($this->storage->spectators as $player)
            $this->onPlayerConnect($player->login, true);
    }

    /**
     * displayWidget(string $login)
     * Refreshes and Displays checpoint counter widget to player

     * * If no login is given, widget is displayed for all players
     * @param string $login|null
     */
    function displayWidget($login = null) {
        if ($login == null)
            CPPanel::EraseAll();
        else
            CPPanel::Erase($login);

        $info = CPPanel::Create($login);
        $info->setSize(30, 6);
        $text = "-  / " . ($this->storage->currentMap->nbCheckpoints - 1);
        $info->setText('$fff' . $text);
        $info->setPosition(0, -68.5);
        $info->show();
    }

    public function onPlayerCheckpoint($playerUid, $login, $timeOrScore, $curLap, $checkpointIndex) {
        CPPanel::Erase($login);

        $info = CPPanel::Create($login);
        $info->setSize(30, 6);
        $text = ($checkpointIndex + 1) . " / " . ($this->storage->currentMap->nbCheckpoints - 1);
        $info->setText('$fff' . $text);
        $info->setPosition(0, -68.5);
        $info->show();
    }

    public function onPlayerFinish($playerUid, $login, $timeOrScore) {
        $this->displayWidget($login);
    }

    public function onEndMatch ($rankings, $winnerTeamOrMap) {
        CPPanel::EraseAll();
    }

    function onPlayerConnect($login, $isSpectator) {
        $this->displayWidget($login);
    }

    function onPlayerDisconnect($login) {
        CPPanel::Erase($login);
    }

}
?>

