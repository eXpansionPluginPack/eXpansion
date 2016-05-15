<?php

namespace ManiaLivePlugins\eXpansion\Quiz\Structures;

class QuizPlayer extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure
{

    public $nickName;
    public $login;
    public $points = 0;

    public function __construct($login, $nick, $points)
    {
        $this->login = $login;
        $this->nickName = $nick;
        $this->points = $points;
    }

}
