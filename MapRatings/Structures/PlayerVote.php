<?php

namespace ManiaLivePlugins\eXpansion\MapRatings\Structures;

class PlayerVote extends \DedicatedApi\Structures\AbstractStructure {

    public $login;
    public $vote;

    function __construct($login = null, $vote = null) {
        $this->login = $login;
        $this->vote = $vote;
    }

}

?>
