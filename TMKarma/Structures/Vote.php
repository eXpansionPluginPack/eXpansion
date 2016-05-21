<?php

namespace ManiaLivePlugins\eXpansion\TMKarma\Structures;

class Vote
{
    public $login;
    public $vote;

    public function __construct($login, $vote)
    {
        $this->vote = (int)$vote;
        $this->login = $login;
    }
}
