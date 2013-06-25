<?php

namespace ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints;

use \ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Gui\Widgets\BestCpPanel;
use \ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Structures\Checkpoint;

class Widgets_BestCheckpoints extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $bestCps = array();

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
        for ($x = 0; $x < $this->storage->currentMap->nbCheckpoints; $x++) {
            $this->bestCps[] = new Checkpoint($x, "", 0);
        }
        BestCpPanel::$bestTimes = $this->bestCps;

        foreach ($this->storage->players as $player)
            $this->onPlayerConnect($player->login, false);
        foreach ($this->storage->spectators as $player)
            $this->onPlayerConnect($player->login, true);
    }

    /**
     * displayWidget(string $login)
     * @param string $login
     */
    function displayWidget($login = null) {
        $info = BestCpPanel::Create($login);
        $info->setSize(290, 20);
        $info->setPosition(0, 88);
        $info->setAlign("center", "top");
        $info->show();
    }

    public function onBeginMatch() {
        for ($x = 0; $x < $this->storage->currentMap->nbCheckpoints; $x++) {

            $this->bestCps[$x] = new Checkpoint($x, "", 0);
        }
        BestCpPanel::$bestTimes = $this->bestCps;

        foreach ($this->storage->players as $player)
            $this->onPlayerConnect($player->login, false);
        foreach ($this->storage->spectators as $player)
            $this->onPlayerConnect($player->login, true);
    }

    public function onPlayerCheckpoint($playerUid, $login, $timeOrScore, $curLap, $checkpointIndex) {
        if (!isset($this->bestCps[$checkpointIndex]))
            $this->bestCps[$checkpointIndex] = new Checkpoint($checkpointIndex, $this->storage->getPlayerObject($login)->nickName, $timeOrScore);

        if ($this->bestCps[$checkpointIndex]->time > $timeOrScore || $this->bestCps[$checkpointIndex]->time == 0) {
            $this->bestCps[$checkpointIndex] = new Checkpoint($checkpointIndex, $this->storage->getPlayerObject($login)->nickName, $timeOrScore);
            BestCpPanel::$bestTimes = $this->bestCps;
            BestCpPanel::RedrawAll();
        }
    }

    public function onPlayerFinish($playerUid, $login, $timeOrScore) {
        
    }

    public function onEndMatch($rankings, $winnerTeamOrMap) {
        BestCpPanel::EraseAll();
        BestCpPanel::$bestTimes = array();
        $this->bestCps = array();
    }

    function onPlayerConnect($login, $isSpectator) {
        $this->displayWidget($login);
    }

    function onPlayerDisconnect($login, $reason = null) {
        BestCpPanel::Erase($login);
    }

}
?>

