<?php

namespace ManiaLivePlugins\eXpansion\IdleKick;

use ManiaLivePlugins\eXpansion\IdleKick\Config;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;

class IdleKick extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

	private $timeStamps = array();
	private $tickCounter = 0;

	/** @var Config */
	private $config;

	function exp_onReady()
	{
		$this->enableDedicatedEvents();
		$this->enableTickerEvent();
		foreach ($this->storage->players as $player)
			$this->onPlayerConnect($player->login, false);
		foreach ($this->storage->spectators as $player)
			$this->onPlayerConnect($player->login, true);		
	}

	function onPlayerConnect($login, $isSpectator)
	{
		$this->checkActivity($login);
	}

	public function onPlayerDisconnect($login, $reason = null)
	{
		if (array_key_exists($login, $this->timeStamps))
			unset($this->timeStamps[$login]);
	}

	public function onPlayerCheckpoint($playerUid, $login, $timeOrScore, $curLap, $checkpointIndex)
	{
		$this->checkActivity($login);
	}

	function onTick()
	{	
		if ($this->tickCounter % 10 == 0) {
			$this->tickCounter = 0;
			$this->config = Config::getInstance();
			foreach ($this->timeStamps as $playerLogin => $value) {
				if (AdminGroups::isInList($playerLogin) && (time() - $value) > ($this->config->idleMinutes * 60)) {
					$player = $this->storage->getPlayerObject($playerLogin);
					$this->exp_chatSendServerMessage('IdleKick: %s', null, array($player->nickName));
					$this->connection->kick($playerLogin, "Idle Kick");
					unset($this->timeStamps[$playerLogin]);
				}
			}
		}
		$this->tickCounter++;
	}

	function checkActivity($login)
	{
		$this->timeStamps[$login] = time();
	}

	function onPlayerChat($playerUid, $login, $text, $isRegistredCmd)
	{
		$this->checkActivity($login);
	}

}

?>