<?php

namespace ManiaLivePlugins\eXpansion\Dedimania\Structures;

class DediRecord extends \DedicatedApi\Structures\AbstractStructure {

    /** @var string */
    public $login;

    /** @var string */
    public $nickname;

    /** @var int */
    public $time;

    /** @var int */
    public $place = -1;

    /** @var string */
    public $checkpoints = "";

    /** @var int */
    public $maxRank = 15;

    /**
     * 
     * @param string $login
     * @param string $nickname
     * @param int $maxrank;
     * @param int $time
     * @param int $place
     * @param array|string $checkpoints
     */
    public function __construct($login, $nickname, $maxrank, $time, $place = -1, $checkpoints = "") {
	$this->login = $login;
	$this->nickname = $nickname;
	$this->place = intval($place);
	$this->maxRank = intval($maxrank);
	$this->time = intval($time);
	$this->checkpoints = $checkpoints;
	if (is_array($checkpoints))
	    $this->checkpoints = implode(",", $checkpoints);
    }

}

?>
