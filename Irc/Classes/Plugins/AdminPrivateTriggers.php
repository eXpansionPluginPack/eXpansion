<?php

/*
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace ManiaLivePlugins\eXpansion\Irc\Classes\Plugins;

use ManiaLivePlugins\eXpansion\Irc\Classes\IrcBot;

/**
 * Description of AdminPublic
 *
 * @author Petri
 */
class AdminPrivateTriggers implements \ManiaLivePlugins\eXpansion\Irc\Classes\IrcListener {

    /** @var IrcBot */
    private $irc;

    /** @var \Maniaplanet\DedicatedServer\Connection */
    private $connection;

    /** @var ManiaLive\Data\Storage */
    private $storage;
    private $allowedLogins = Array("reaby");

    public function __construct() {
	$config = \ManiaLive\DedicatedApi\Config::getInstance();
	$this->connection = \Maniaplanet\DedicatedServer\Connection::factory($config->host, $config->port);
	$this->storage = \ManiaLive\Data\Storage::getInstance();
    }

    public function irc_onConnect($connection) {
	$this->irc = $connection;
    }

    public function irc_onDisconnect() {
	
    }

    public function irc_onPrivateMessage($connection, $nick, $message) {
	if (!in_array($connection->getIrcNick($nick), $this->allowedLogins)) {
	    $this->irc->sendPublicChat("You are not allowed to use this interface. go away.");
	}
	$params = explode(" ", $message);
	$command = array_shift($params);

	$to = $this->irc->getIrcNick($nick);

	switch ($command) {
	    case "help":
		$this->irc->sendPrivateMessage($to, "Available commands: kick");
		break;
	    case "kick":
		try {
		    $this->connection->kick($params[0]);
		    $this->irc->sendPrivateMessage($to, "Kicked " . $params[0]);
		} catch (\Exception $e) {
		    $this->irc->sendPrivateMessage($to, "Failed to kick:" . $e->getMessage());
		}
		break;
	    default:
		break;
	}
    }

    public function irc_onPublicChat($connection, $channel, $nick, $message) {
	
    }

}
