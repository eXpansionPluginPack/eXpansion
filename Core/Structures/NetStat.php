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
    public $packetLossRate;
    public $latestNetworkActivity;
    public $ipAddress;

    public function __construct(\Maniaplanet\DedicatedServer\Structures\PlayerNetInfo $player) {
	$this->login = $player->login;
	$this->updateLatency = $player->stateUpdateLatency;
	$this->updatePeriod = $player->stateUpdatePeriod;
	$this->packetLossRate = $player->packetLossRate;
	$this->latestNetworkActivity = $player->latestNetworkActivity;
	$this->ipAddress = $player->iPAddress;
    }


}
