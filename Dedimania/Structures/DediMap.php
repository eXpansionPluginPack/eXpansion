<?php

namespace ManiaLivePlugins\eXpansion\Dedimania\Structures;

class DediMap extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure {

    /** @var string|null */
    public $uId = null;

    /** @var int */
    public $mapMaxRank = 15;

    /** @var string */
    public $allowedGameModes = "TA,Rounds";

    public function __construct($uid, $maxrank, $allowedgamemodes) {
	$this->uId = $uid;
	$this->mapMaxRank = $maxrank;
	$this->allowedGameModes = $allowedgamemodes;
    }

}

?>
