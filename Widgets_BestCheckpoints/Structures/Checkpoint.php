<?php

namespace ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Structures;

class Checkpoint extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure
{

    public $time = 0;
    public $login = 0;
    public $nickname = "";
    public $index;

    public function __construct($index, $login, $nickname, $time)
    {
        $this->time = $time;
        $this->login = $login;
        $this->nickname = $nickname;
        $this->index = $index;
    }
}
