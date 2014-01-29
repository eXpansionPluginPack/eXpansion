<?php

namespace ManiaLivePlugins\eXpansion\Core\Structures;

/**
 * Description of NetStat
 *
 * @author Petri
 */
class NetStat extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure {
    public $login;
    public $updateLatency;
    public $updatePeriod;
    
    public function __construct(\Maniaplanet\DedicatedServer\Structures\Player $player) {
	$this->login = $player->login;
	$this->updateLatency = $player->stateUpdateLatency;
	$this->updatePeriod = $player->stateUpdatePeriod;
    }
    
    
}
