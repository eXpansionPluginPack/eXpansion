<?php
namespace ManiaLivePlugins\eXpansion\Core\Structures;
use Maniaplanet\DedicatedServer\Structures\AbstractStructure;
use Maniaplanet\DedicatedServer\Structures\PlayerNetInfo;

/**
 * Description of NetStat
 *
 * @author Petri
 */
class NetStat extends AbstractStructure
{
    public $login;
    public $updateLatency;
    public $updatePeriod;
    public $packetLossRate;
    public $latestNetworkActivity;
    public $ipAddress;

    public function __construct(PlayerNetInfo $player)
    {
        $this->login = $player->login;
        $this->updateLatency = $player->stateUpdateLatency;
        $this->updatePeriod = $player->stateUpdatePeriod;
        $this->packetLossRate = $player->packetLossRate;
        $this->latestNetworkActivity = $player->latestNetworkActivity;
        $this->ipAddress = $player->iPAddress;
    }
}
