<?php

/*
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

namespace ManiaLivePlugins\eXpansion\AutoQueue;

use Exception;
use ManiaLive\Data\Player;
use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\AutoQueue\Classes\Queue;
use ManiaLivePlugins\eXpansion\AutoQueue\Gui\Widgets\EnterQueueWidget;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\MatchMakingLobby\Windows\Dialog;
use Maniaplanet\DedicatedServer\Structures\Status;

/**
 * Description of AutoQueue
 *
 * @author Reaby
 */
class AutoQueue extends ExpPlugin
{

	/** @var Queue */
	private $queue;

	private $widget_enterQueue;

	public function exp_onLoad()
	{
		$aHandler = ActionHandler::getInstance();
		EnterQueueWidget::$action_toggleQueue = $aHandler->CreateAction(array($this, "enterQueue"));
	}

	public function exp_onReady()
	{
		$this->enableDedicatedEvents();
		$this->queue = new Queue();

		foreach ($this->storage->spectators as $login => $player) {
			$this->connection->forceSpectator($login, 1);
			$this->showEnterQueue($login);
		}
		$this->widgetSyncList();

		//$this->registerChatCommand("next", "queueReleaseNext", 0, true);
	}

	function onPlayerConnect($login, $isSpectator)
	{
		if ($isSpectator) {
			$this->connection->forceSpectator($login, 1);
			$this->showEnterQueue($login);
			$this->widgetSyncList();
		}
	}

	public function onPlayerInfoChanged($info)
	{
		if ($this->storage->serverStatus->code != Status::PLAY) return;

		$player = \Maniaplanet\DedicatedServer\Structures\PlayerInfo::fromArray($info);
		$login = $player->login;

		if ($player->spectator) {
			$this->connection->forceSpectator($login, 1);
			if ($player->hasPlayerSlot) {
				try {
					$this->connection->spectatorReleasePlayerSlot($login);
				} catch (\Exception $e) {
					
				}
			}
			$this->showEnterQueue($login);

			if($this->storage->server->currentMaxPlayers  > count($this->storage->players)){
				$this->queueReleaseNext();
			}
		}
		else {
			EnterQueueWidget::Erase($login);
		}

		$this->widgetSyncList();
	}

	public function onPlayerDisconnect($login, $disconnectionReason = null)
	{
		if (in_array($login, $this->queue->getLogins())) {
			$this->queue->remove($login);
		}
		$this->queueReleaseNext();
	}

	function onBeginMatch()
	{
		$this->queRealeseAvailable();
	}

	function onBeginRound()
	{
		$this->queRealeseAvailable();
	}

	public function queRealeseAvailable(){
		$count = $this->storage->server->currentMaxPlayers;
		for($i = 0; $i < $count; $i++){
			$this->queueReleaseNext();
		}
	}


	public function queueReleaseNext()
	{
		$player = $this->queue->getNextPlayer();
		if ($player) {
			$this->connection->forceSpectator($player->login, 2);
			$this->connection->forceSpectator($player->login, 0);
			$msg = exp_getMessage('You got free spot, good luck and have fun!');
			$this->exp_chatSendServerMessage($msg, $player->login);
		}
		$this->widgetSyncList();
	}

	public function enterQueue($login)
	{
		$this->queue->add($login);

		echo $this->storage->server->currentMaxPlayers. " < ".count($this->storage->players)."\n";

		if($this->storage->server->currentMaxPlayers  > count($this->storage->players)){
			$this->queueReleaseNext();
		}else{
			EnterQueueWidget::Erase($login);
			$this->widgetSyncList();
		}
	}

	public function exp_onUnload()
	{
		EnterQueueWidget::$action_toggleQueue = null;
		EnterQueueWidget::EraseAll();
		Gui\Widgets\QueueList::EraseAll();
		$this->queue = null;
	}

	public function widgetSyncList()
	{
		$this->queue->syncPlayers(array_keys($this->storage->players));

		Gui\Widgets\QueueList::EraseAll();

		$widget = Gui\Widgets\QueueList::Create();
		$widget->setPlayers($this->queue->getQueuedPlayers());
		$widget->setPosition(0, 0);
		$widget->setSize(60, 30);
		$spectators = array_keys($this->storage->spectators);
		if (!empty($spectators)) {
			$widget->show($spectators);
		}
	}

	public function showEnterQueue($login)
	{
		if (in_array($login, $this->queue->getLogins()))
			return;

		$widget = EnterQueueWidget::Create($login);
		$widget->setPosition(0, 30);
		$widget->show($login);
	}

}
