<?php

namespace ManiaLivePlugins\eXpansion\Core;

use ManiaLive\DedicatedApi\Callback\Event as dediEvent;
use ManiaLive\Event\Dispatcher;
use ManiaLive\Utilities\Console;
use ManiaLivePlugins\eXpansion\Core\Structures\Query;
use ManiaLivePlugins\eXpansion\Core\Structures\Callback;

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
		$this->connection = \Maniaplanet\DedicatedServer\Connection::factory($config->host, $config->port);
		$this->storage = \ManiaLive\Data\Storage::getInstance();
		Dispatcher::register(dediEvent::getClass(), $this, dediEvent::ALL, 1);
		$this->relayMaster = $this->connection->getMainServerPlayerInfo();
		$this->onPlayerConnect(null, true);
		$this->queryMaster("syncMap", array(null), "xSyncMap");
		$this->queryMaster("syncMapNext", array(null), "xSyncMapNext");
	}

	public function sendRelay($data)
	{
		$data = gzdeflate(serialize($data));
		$this->connection->tunnelSendData(implode(",", $this->connectedRelays), $data);
	}

	public function sendMaster($data)
	{
		if (!$this->isMaster())
			return;

		$data = gzdeflate(serialize($data));
		$this->connection->tunnelSendData($this->relayMaster, $data);
	}

	public function queryMaster($method, $value, $callback)
	{
		if ($this->isMaster())
			return;
		echo "Querying: $method";

		$data = gzdeflate(serialize(new Query($method, $value, $callback, $this->storage->serverLogin)));
		$this->connection->tunnelSendData($this->relayMaster, $data);
	}

	public function queryRelay($method, $data, $callback)
	{

	}

	private function syncMap($params)
	{

		echo "onSync Map:" . $params;
		$this->storage = \ManiaLive\Data\Storage::getInstance();
		//var_dump($this->storage->currentMap);
		return $this->storage->currentMap;
	}

	private function syncMapNext($params)
	{
		echo "onSync Nextmap:" . $params;
		$this->storage = \ManiaLive\Data\Storage::getInstance();
		//var_dump($this->storage->nextMap);
		return $this->storage->nextMap;
	}

	private function xSyncMap(\Maniaplanet\DedicatedServer\Structures\Map $map)
	{
		echo "xSyncmap: " . $map->name . "\n";
		///var_dump($map);
		$this->storage = \ManiaLive\Data\Storage::getInstance();
		$this->storage->currentMap = $map;
	}

	private function xSyncMapNext(\Maniaplanet\DedicatedServer\Structures\Map $map)
	{
		echo "xSyncNextmap: " . $map->name . "\n";
		//var_dump($map);

		$this->storage = \ManiaLive\Data\Storage::getInstance();
		$this->storage->nextMap = $map;
	}

	public function isMaster()
	{
		if ($this->storage->serverLogin == $this->relayMaster)
			return true;
		return false;
	}

	final public function onBeginMap($map, $warmUp, $matchContinuation)
	{
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

		$this->connectedRelays = array();
		foreach ($this->connection->getPlayerList(-1, 0, 2) as $spec) {
			if ($spec->isServer == true && $spec->login != $this->relayMaster && $spec->login != $this->storage->serverLogin) {
				print('[eXpansion Pack] Found a relay, Login:		' . $spec->login . "\n");
				print('[eXpansion Pack] Found a relay, ServerName:	' . $spec->nickName . "\n");
				$this->connectedRelays[] = $spec->login;
			}
			if ($spec->isServer == true && $spec->login == $this->relayMaster) {
				print('[eXpansion Pack] Found a master, Login:		' . $spec->login . "\n");
				print('[eXpansion Pack] Found a master, ServerName:	' . $spec->nickName . "\n");
			}
		}
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
		$obj = unserialize(gzinflate($data));

		echo "recieved tunnelData!!";
	//	var_dump($obj);

		if ($obj instanceof \ManiaLivePlugins\eXpansion\Core\Structures\Callback) {
			try {
				echo "Callback:";
			//	var_dump($obj);

				if (is_array($obj->method)) {
					echo "trying to call";
					call_user_func_array($obj->method, $obj->params);
				}
				else {
					echo "trying to call:" . $obj->method;
					call_user_func_array(array($this, $obj->method), $obj->params);
				}
			} catch (\Exception $e) {
				echo "Couldn't execute! " . $e->getMessage();
			}
		}

		if ($obj instanceof \ManiaLivePlugins\eXpansion\Core\Structures\Query) {
			if ($obj->from == $this->storage->serverLogin)
				return;

			try {
				echo "Query:" . $login;
				$ret = "not defined";
				if (is_array($obj->method)) {
					$ret = call_user_func_array($obj->method, $obj->params);
				}
				else {
					$ret = call_user_func_array(array($this, $obj->method), $obj->params);
				}
				echo "RETRURNING: to " . $obj->from . "\n";
			//	var_dump($ret);

				$data = gzdeflate(serialize(new Callback($obj->callback, array($ret))));
				$this->connection->tunnelSendData($obj->from, $data);
			} catch (\Exception $e) {
				echo "Couldn't query! " . $e->getMessage();
			}
		}
	}

	final public function onVoteUpdated($stateName, $login, $cmdName, $cmdParam)
	{

	}

}
?>

