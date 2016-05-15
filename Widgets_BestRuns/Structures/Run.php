<?php

namespace ManiaLivePlugins\eXpansion\Widgets_BestRuns\Structures;

use Maniaplanet\DedicatedServer\Structures\AbstractStructure;
use Maniaplanet\DedicatedServer\Structures\PlayerRanking;

class Run extends AbstractStructure
{

    public $totalTime = 0;

    public $nickname = "";

    public $player;

    public $checkpoints = array();

    function __construct(PlayerRanking $player)
    {
        $this->player = $player;
        $this->totalTime = $player->bestTime;
        $this->nickname = $player->nickName;
        $this->checkpoints = $player->bestCheckpoints;
    }

}

