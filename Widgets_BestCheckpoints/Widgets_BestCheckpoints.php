<?php

namespace ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints;

use \ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Gui\Widgets\BestCpPanel;
use \ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Structures\Checkpoint;

class Widgets_BestCheckpoints extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $bestCps;

    function exp_onInit() {
	$this->exp_addTitleSupport("TM");
	$this->exp_addTitleSupport("Trackmania");
	//Important for all eXpansion plugins.
	$this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_ROUNDS);
	$this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK);
	$this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM);
	$this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS);
	$this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP);
	$this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT, 'TeamAttack.Script.txt');
    }

    function exp_onLoad() {
	$this->enableDedicatedEvents();
    }

    public function exp_onReady() {
	$this->onBeginMatch();
    }

    /**
     * displayWidget(string $login)
     * @param string $login
     */
    function displayWidget($login = null) {
	$info = BestCpPanel::Create($login);
	$info->setSize(190, 7);
	$info->setPosition(-112, 90);
	$info->setAlign("center", "top");
	$info->show();
    }

    public function onBeginMatch() {
	$this->bestCps = new \SplFixedArray($this->storage->currentMap->nbCheckpoints);
	for ($x = 0; $x < $this->storage->currentMap->nbCheckpoints; $x++) {
	    $this->bestCps[$x] = new Checkpoint($x, "", "", 0);
	}
	BestCpPanel::$bestTimes = $this->bestCps;

	$this->displayWidget(null);
    }

    /* public function onPlayerCheckpoint($playerUid, $login, $timeOrScore, $curLap, $checkpointIndex) {
      $checkpointIndex = $checkpointIndex % $this->storage->currentMap->nbCheckpoints;


      // It only happens when multilap but fix on the top should fix this
      // if (!isset($this->bestCps[$checkpointIndex]))
      //     $this->bestCps[$checkpointIndex] = new Checkpoint($checkpointIndex, $this->storage->getPlayerObject($login)->nickName, $timeOrScore);


      if ($this->bestCps[$checkpointIndex]->time > $timeOrScore || $this->bestCps[$checkpointIndex]->time == 0) {
      $this->bestCps[$checkpointIndex] = new Checkpoint($checkpointIndex, $login, $this->storage->getPlayerObject($login)->nickName, $timeOrScore);
      //BestCpPanel::RedrawAll();
      }
      } */

    public function onEndMatch($rankings, $winnerTeamOrMap) {
	BestCpPanel::EraseAll();
	BestCpPanel::$bestTimes = array();
	$this->bestCps = array();
    }

}
?>

