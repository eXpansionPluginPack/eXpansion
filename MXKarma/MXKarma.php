<?php

/*
 * Copyright (C) 2014 Reaby
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace ManiaLivePlugins\eXpansion\MXKarma;

use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Core\types\config\types\String;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj;
use ManiaLivePlugins\eXpansion\MXKarma\Classes\Connection as mxConnection;
use ManiaLivePlugins\eXpansion\MXKarma\Events\MXKarmaEvent;
use ManiaLivePlugins\eXpansion\MXKarma\Events\MXKarmaEventListener;
use ManiaLivePlugins\eXpansion\MXKarma\Gui\Widgets\MXRatingsWidget;
use ManiaLivePlugins\eXpansion\MXKarma\Structures\MXRating;
use ManiaLivePlugins\eXpansion\MXKarma\Structures\MXVote;

/**
 * Description of MXKarma
 *
 * @author Reaby
 */
class MXKarma extends ExpPlugin implements MXKarmaEventListener
{

	/** @var Connection */
	private $mxConnection;

	private $mapTime = 0;

	private $mapStart = -1;

	/** @var MXRating */
	private $mxRatings = null;

	/** @var String[][] */
	private $votes = array();

	private $votesTemp = array();

	/** @var MXVote */
	private $msg_error, $msg_connected;

	/** @var Config */
	private $config;

	private $settingsChanged = array();

	public function exp_onLoad()
	{
		parent::exp_onLoad();
		$this->config = Config::getInstance();
		$this->mxConnection = new mxConnection();
		$this->msg_error = exp_getMessage('MXKarma error %1$s: %2$s');
		$this->msg_connected = exp_getMessage('MXKarma connection Success!');
	}

	public function exp_onReady()
	{
		$this->enableDedicatedEvents();
		\ManiaLive\Event\Dispatcher::register(MXKarmaEvent::getClass(), $this);


		$this->mapStart = time();
		$this->tryConnect();
	}

	private function tryConnect()
	{
		$admins = AdminGroups::getInstance();
		$this->config = Config::getInstance();
		if (!$this->mxConnection->isConnected()) {
			if (empty($this->config->mxKarmaServerLogin) || empty($this->config->mxKarmaApiKey)) {
				$admins->announceToPermission(
						Permission::expansion_pluginSettings, "#admin_error#Server login or/and Server code is empty in MXKarma Configuration"
				);
				$this->console("Server code or/and login is not configured for MXKarma plugin!");
				return;
			}
			$this->mxConnection->connect($this->config->mxKarmaServerLogin, $this->config->mxKarmaApiKey);
		}
		else {
			$admins->announceToPermission(
					Permission::expansion_pluginSettings, "#admin_error#Tried to connect to MXKarma, but connection is already made."
			);
			$this->console("Tried to connect to MXKarma, but connection is already made.");
		}
	}

	public function onSettingsChanged(\ManiaLivePlugins\eXpansion\Core\types\config\Variable $var)
	{
		$this->settingsChanged[$var->getName()] = true;
		if (array_key_exists("mxKarmaApiKey", $this->settingsChanged) && array_key_exists("mxKarmaServerLogin", $this->settingsChanged)) {
			$this->tryConnect();
			$this->settingsChanged = array();
		}
	}

	public function onPlayerChat($playerUid, $login, $text, $isRegistredCmd)
	{
		if ($playerUid == 0)
			return;
		if ((substr($text, 0, 1) == "+" || substr($text, 0, 1) == "-")) {

			$player = $this->storage->getPlayerObject($login);
			switch ($text) {
				case "+++":
					$this->votesTemp[$login] = new MXVote($player, 100);
					break;

				case "++":
					$this->votesTemp[$login] = new MXVote($player, 80);
					break;

				case "+":
					$this->votesTemp[$login] = new MXVote($player, 60);
					break;
				case "+-":
				case "-+":
					$this->votesTemp[$login] = new MXVote($player, 50);
					break;
				case "-":
					$this->votesTemp[$login] = new MXVote($player, 40);
					break;

				case "--":
					$this->votesTemp[$login] = new MXVote($player, 20);
					break;

				case "---":
					$this->votesTemp[$login] = new MXVote($player, 0);
					break;
			}

			$this->exp_chatSendServerMessage("Vote registered for MXKarma", $login);
		}
	}

	public function onBeginMatch()
	{
		parent::onBeginMatch();
		$this->mxRatings = null;
		$this->votes = array();
		$this->votesTemp = array();
		$this->mapStart = time();
		if ($this->mxConnection->isConnected()) {
			$this->mxConnection->getRatings($this->getPlayers(), false);
		}
	}

	public function onEndMatch($rankings, $winnerTeamOrMap)
	{

		$newVotes = array();

		foreach ($this->votesTemp as $login => $vote) {
			$oldVote = ArrayOfObj::getObjbyPropValue($this->votes, "login", $login);
			// if oldvote was found
			if ($oldVote) {
				// compare if the new vote and the old vote differs
				if ($oldVote->vote != $vote->vote) {
					$newVotes[] = $vote;
				}
			}
			// othervice cast it as new vote
			else {
				$newVotes[] = $vote;
			}
		}

		if (count($newVotes) > 0) {
			$outArray = array();
			foreach ($newVotes as $login => $vote) {
				$outArray[] = $vote;
			}

			$this->mxConnection->saveVotes($this->storage->currentMap, time() - $this->mapStart, $outArray);
		}

		MXRatingsWidget::EraseAll();
	}

	public function getPlayers()
	{
		$players = array();
		$players = array_keys($this->storage->players);
		array_merge($players, array_keys($this->storage->players));
		return $players;
	}

	public function MXKarma_onConnected()
	{
		$this->mxConnection->getRatings($this->getPlayers(), false);
	}

	public function MXKarma_onDisconnected()
	{
		
	}

	public function MXKarma_onError($state, $number, $reason)
	{
		$this->exp_chatSendServerMessage($this->msg_error, null, array($state, $reason));
		$this->console("MXKarma error  " . $state . ": " . $reason);
	}

	public function MXKarma_onVotesRecieved(MXRating $votes)
	{
		if ($this->mxRatings === null) {
			$this->mxRatings = $votes;
			$this->votes = Array();
			foreach ($votes->votes as $vote) {
				$this->votes[] = $vote;
			}
		}
		else {
			$this->mxRatings->append($votes);
			foreach ($votes->votes as $vote) {
				$this->votes[] = $vote;
			}
		}

		$widget = MXRatingsWidget::Create();
		$widget->setRating($this->mxRatings->voteaverage, $this->mxRatings->votecount);
		$widget->show();
	}

	public function MXKarma_onVotesSave($isSuccess)
	{
		if ($isSuccess) {
			$this->exp_chatSendServerMessage("MXKarma saved successfully!", null);
		}
	}

	public function exp_onUnload()
	{
		\ManiaLive\Event\Dispatcher::unregister(MXKarmaEvent::getClass(), $this);
		MXRatingsWidget::EraseAll();
		unset($this->mxConnection);
		parent::exp_onUnload();
	}

}
