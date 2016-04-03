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
    /** @var \SplQueue */
    private $queue;

    /** @var Storage */
    private $storage;

    public function __construct()
    {
        $this->storage = Storage::getInstance();
        $this->queue = new \SplQueue();
    }

    public function syncPlayers($logins)
    {
        if (is_array($logins)) {
            array_map(array($this, "remove"), $logins);
        } else {
            $this->remove($login);
        }
    }

    public function add($login)
    {
        if (in_array($login, $this->getLogins())) {
            //       echo "can't add, since player is already in queue\n";
            return;
        }

        $player = $this->storage->getPlayerObject($login);
        $this->queue->enqueue($player);
    }

    public function remove($login)
    {
        foreach ($this->queue as $idx => $player) {
            if ($player->login == $login) {
                if ($this->queue->offsetExists($idx)) {
                    $this->queue->offsetUnset($idx);
                }
            }
        }
    }

    /**
     * gets first player out of the array :)
     * @return null|QueuePlayer
     */
    public function getNextPlayer()
    {

        if ($this->queue->count() > 0) {
            return $this->queue->dequeue();
        }
    }

    /**
     * gets list of queueplayer
     * @return QueuePlayer[]
     */
    public function getQueuedPlayers()
    {
        $out = array();
        foreach ($this->queue as $player)
            $out[$player->login] = $player;

        return $out;
    }

    /**
     * gets list of logins
     * @return string[]
     */
    public function getLogins()
    {
        return array_keys($this->getQueuedPlayers());
    }
}