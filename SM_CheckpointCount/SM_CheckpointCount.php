<?php

namespace ManiaLivePlugins\eXpansion\SM_CheckpointCount;

use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\SM_CheckpointCount\Gui\Widgets\CPPanel;

class SM_CheckpointCount extends ExpPlugin
{

	public function exp_onReady()
	{
		$this->enableDedicatedEvents();
		$this->enableScriptEvents();

		foreach ($this->storage->players as $player)
			$this->onPlayerConnect($player->login, false);
		foreach ($this->storage->spectators as $player)
			$this->onPlayerConnect($player->login, true);
	}

	/**
	 * displayWidget(string $login)
	 * Refreshes and Displays checpoint counter widget to player

	 * * If no login is given, widget is displayed for all players
	 * @param string $login|null
	 */
	function displayWidget($login = null, $cpIndex = "-")
	{
		if ($login == null)
			CPPanel::EraseAll();
		else
			CPPanel::Erase($login);

		$info = CPPanel::Create($login);
		$info->setSize(35, 6);
		$text = $cpIndex . " / " . ($this->storage->currentMap->nbCheckpoints - 1);
		$info->setText('$fff' . $text);
		$info->setPosition(-17.5, -60);
		$info->show();
	}

	public function LibXmlRpc_OnWayPoint($login, $blockId, $time, $cpIndex, $isEndBlock, $lapTime, $lapNb, $isLapEnd)
	{
		$cp = $cpIndex;
		if ($isEndBlock)
			$cp = "-";
		$this->displayWidget($login, $cp);
	}

	public function LibXmlRpc_onEndMatch($rankings, $winnerTeamOrMap)
	{
		CPPanel::EraseAll();
	}

	public function onBeginMatch()
	{
		foreach ($this->storage->players as $player)
			$this->onPlayerConnect($player->login, false);
		foreach ($this->storage->spectators as $player)
			$this->onPlayerConnect($player->login, true);
	}

	function onPlayerConnect($login, $isSpectator)
	{
		$this->displayWidget($login);
	}

	function onPlayerDisconnect($login, $reason = null)
	{
		CPPanel::Erase($login);
	}

}
?>

