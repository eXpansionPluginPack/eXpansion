<?php

namespace ManiaLivePlugins\eXpansion\MapRatings\Structures;

class PlayerVote extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure
{

    public $login;
    public $vote;

    function __construct($login = null, $vote = null)
    {
        $this->login = $login;
        $this->vote = $vote;
    }

}
