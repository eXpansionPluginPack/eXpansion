<?php

namespace ManiaLivePlugins\eXpansion\Widgets_BestRuns\Structures;

class Run extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure {

    public $totalTime = 0;
    public $nickname = "";
    public $player;
    public $checkpoints = array();

    function __construct(\ManiaLive\Data\Player $player) {
	$this->player = $player;
	$this->totalTime = $player->bestTime;
	$this->nickname = $player->nickName;
	$this->checkpoints = $player->bestCheckpoints;
    }

}

?>
