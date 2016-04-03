<?php

namespace ManiaLivePlugins\eXpansion\Overlay_TeamScores\Structures;

class Team extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure
{

    public $name = "";
    public $leader = null;
    public $members = array();
    public $score = 0;

    public function __construct($name = "")
    {
        $this->name = $name;
    }

}