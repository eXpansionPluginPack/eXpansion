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

namespace ManiaLivePlugins\eXpansion\AutoQueue\Classes;

use ManiaLive\Data\Storage;
use ManiaLivePlugins\eXpansion\AutoQueue\Structures\QueuePlayer;

/**
 * Description of Queue
 *
 * @author Reaby
 */
class Queue
{

	/** @var QueuePlayer[] */
	private $queue = array();

	/** @var Storage */
	private $storage;

	public function __construct()
	{
		$this->storage = Storage::getInstance();
	}

	public function syncPlayers($logins)
	{
		if (is_array($logins)) {
			array_map(array($this, "remove"), $logins);
		}
	}

	public function add($login)
	{
		$player = $this->storage->getPlayerObject($login);

		if (!array_key_exists($login, $this->queue)) {
			if ($player->ladderScore >= $this->storage->server->ladderServerLimitMin) {
				$qPlayer = QueuePlayer::fromArray($player->toArray());
				$qPlayer->queuePosition = count($this->queue);
				$this->queue[$login] = $qPlayer;
			}
		}
		print_r(array_keys($this->queue));
	}

	public function remove($login)
	{
		if (array_key_exists($login, $this->queue)) {
			unset($this->queue[$login]);
		}
		print_r(array_keys($this->queue));
	}

	/**
	 * gets first player out of the array :)
	 * @return null|QueuePlayer
	 */
	public function getNextPlayer()
	{
		$player = array_shift($this->queue);
		print_r(array_keys($this->queue));
		return $player;
	}

	/**
	 * gets list of queueplayer
	 * @return QueuePlayer[] 
	 */
	public function getQueuedPlayers()
	{
		return $this->queue;
	}

	/**
	 * gets list of logins
	 * @return string[]
	 */
	public function getLogins()
	{
		return array_keys($this->queue);
	}

}
