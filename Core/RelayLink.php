<?php

namespace ManiaLivePlugins\eXpansion\Core;

use ManiaLive\DedicatedApi\Callback\Event as dediEvent;
use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\Helpers\Helper;

class RelayLink extends \ManiaLib\Utils\Singleton implements \ManiaLive\DedicatedApi\Callback\Listener
{

    public static $started = false;

    /** @var  \Maniaplanet\DedicatedServer\Connection */
    private $connection;

    /** @var \ManiaLive\Data\Storage */
    private $storage;

    private $connectedRelays = array();

    private $relayMaster = null;

    public function __construct()
    {
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \ManiaLivePlugins\eXpansion\Helpers\Singletons::getInstance()->getDediConnection();
        $this->storage = \ManiaLive\Data\Storage::getInstance();
        Dispatcher::register(dediEvent::getClass(), $this, dediEvent::ALL, 1);
        $this->relayMaster = $this->connection->getMainServerPlayerInfo();
        $this->onPlayerConnect(null, true);
        $this->syncCarData();
        $this->queryMaster("syncMap", array(null), "xSyncMap");
        $this->queryMaster("syncMapNext", array(null), "xSyncMapNext");
    }

    public function syncCarData()
    {
        $dir = $this->connection->getMapsDirectory();
        try {

            $gbxMap = new \ManiaLivePlugins\eXpansion\Helpers\GbxReader\Map();
            $infoCurrent = $gbxMap->read(
                $dir . DIRECTORY_SEPARATOR . $this->storage->currentMap->fileName
            );
            $this->storage->currentMap->playerModel = $infoCurrent->playerModel;
            $infoNext = $gbxMap->read(
                $dir . DIRECTORY_SEPARATOR . $this->storage->nextMap->fileName
            );
            $this->storage->currentMap->playerModel = $infoNext->playerModel;
        } catch (\Exception $e) {
            Helper::log("error while reading map: " . $dir . DIRECTORY_SEPARATOR . $this->storage->currentMap->fileName, array('eXpansion', 'Core', 'RelayLink'));
            Helper::log("mapreader said: " . $e->getMessage(), array('eXpansion', 'Core', 'RelayLink'));
        }
    }

    public function sendRelay($data)
    {
    }

    public function sendMaster($data)
    {
    }

    public function queryMaster($method, $value, $callback)
    {

    }

    public function queryRelay($method, $data, $callback)
    {

    }

    private function syncMap($params)
    {

        $this->storage = \ManiaLive\Data\Storage::getInstance();

        return $this->storage->currentMap;
    }

    private function syncMapNext($params)
    {
        $this->storage = \ManiaLive\Data\Storage::getInstance();

        return $this->storage->nextMap;
    }

    private function xSyncMap(\Maniaplanet\DedicatedServer\Structures\Map $map)
    {
        $this->storage = \ManiaLive\Data\Storage::getInstance();
        $this->storage->currentMap = $map;
    }

    private function xSyncMapNext(\Maniaplanet\DedicatedServer\Structures\Map $map)
    {
        $this->storage = \ManiaLive\Data\Storage::getInstance();
        $this->storage->nextMap = $map;
    }

    public function isMaster()
    {
        if ($this->storage->serverLogin == $this->relayMaster) {
            return true;
        }

        return false;
    }

    final public function onBeginMap($map, $warmUp, $matchContinuation)
    {
        $this->syncCarData();
        $this->queryMaster("syncMap", array(null), "xSyncMap");
        $this->queryMaster("syncMapNext", array(null), "xSyncMapNext");
    }

    final public function onBeginMatch()
    {

    }

    final public function onBeginRound()
    {

    }

    final public function onBillUpdated($billId, $state, $stateName, $transactionId)
    {

    }

    final public function onEcho($internal, $public)
    {

    }

    final public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap)
    {

    }

    final public function onEndMatch($rankings, $winnerTeamOrMap)
    {

    }

    final public function onEndRound()
    {

    }

    final public function onManualFlowControlTransition($transition)
    {

    }

    final public function onMapListModified($curMapIndex, $nextMapIndex, $isListModified)
    {

    }

    final public function onModeScriptCallback($param1, $param2)
    {

    }

    final public function onPlayerAlliesChanged($login)
    {

    }

    final public function onPlayerChat($playerUid, $login, $text, $isRegistredCmd)
    {

    }

    final public function onPlayerCheckpoint($playerUid, $login, $timeOrScore, $curLap, $checkpointIndex)
    {

    }

    final public function onPlayerConnect($login, $isSpectator)
    {

    }

    final public function onPlayerDisconnect($login, $disconnectionReason)
    {

    }

    final public function onPlayerFinish($playerUid, $login, $timeOrScore)
    {

    }

    final public function onPlayerIncoherence($playerUid, $login)
    {

    }

    final public function onPlayerInfoChanged($playerInfo)
    {

    }

    final public function onPlayerManialinkPageAnswer($playerUid, $login, $answer, array $entries)
    {

    }

    final public function onServerStart()
    {

    }

    final public function onServerStop()
    {

    }

    final public function onStatusChanged($statusCode, $statusName)
    {

    }

    final public function onTunnelDataReceived($playerUid, $login, $data)
    {

    }

    final public function onVoteUpdated($stateName, $login, $cmdName, $cmdParam)
    {

    }
}
