<?php

namespace ManiaLivePlugins\eXpansion\Widgets_BestRuns;

use ManiaLivePlugins\eXpansion\Widgets_BestRuns\Structures\Run;
use ManiaLivePlugins\eXpansion\Widgets_BestRuns\Gui\Widgets\BestRunPanel;

/**
 * Description of Widgets_BestRuns
 *
 * @author Reaby
 */
class Widgets_BestRuns extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $bestTime = 0;
    private $nbDisplay = 1;

    function exp_onInit() {
	$this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_ROUNDS);
	$this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TIMEATTACK);
	$this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TEAM);
	$this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_LAPS);
	$this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_CUP);
    }

    function exp_onLoad() {
	$this->enableDedicatedEvents();
	$this->enableStorageEvents();
    }

    public function exp_onReady() {
	$this->onBeginMatch();
	
	$this->onPlayerNewBestTime(null, -1, 0);
    }

    public function onBeginMatch() {
	$this->bestTime = 0;
	BestRunPanel::$bestRuns = array();

	foreach ($this->storage->players as $player)
	    $this->onPlayerConnect($player->login, false);
	foreach ($this->storage->spectators as $player)
	    $this->onPlayerConnect($player->login, true);
    }

    public function onPlayerNewBestTime($player, $oldBest, $newBest) {
	// othervice if the players new best time is faster than the buffer, update
	if ($this->bestTime == 0 || $newBest < $this->bestTime) {

	    $this->bestTime = $newBest;
	    BestRunPanel::$bestRuns = array();
	    $ranking = $this->connection->getCurrentRanking($this->nbDisplay, 0);
	    foreach ($ranking as $player) {
		BestRunPanel::$bestRuns[] = new Run($player);
	    }
	    BestRunPanel::RedrawAll();
	}
    }

    public function onEndMatch($rankings, $winnerTeamOrMap) {
	BestRunPanel::EraseAll();
	$this->bestTime = 0;
	BestRunPanel::$bestRuns = array();
    }

    /**
     * displayWidget(string $login)
     * @param string $login
     */
    function displayWidget($login = null) {
	$info = BestRunPanel::Create($login);
	$info->setSize(220, 20);
	$info->setPosition(0, 86);
	$info->setAlign("center", "top");
	$info->show();
    }

    function onPlayerConnect($login, $isSpectator) {
	$this->displayWidget($login);
    }

    function onPlayerDisconnect($login, $reason = null) {
	BestRunPanel::Erase($login);
    }

}
