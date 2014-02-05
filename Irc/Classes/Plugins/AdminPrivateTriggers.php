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
	$to = $this->irc->getIrcNick($nick);
	if (!in_array($connection->getIrcNick($nick), $this->allowedLogins)) {
	    $this->irc->sendPrivateMessage($to, "You are not allowed to use this interface. go away.");
	    return;
	}
	$params = explode(" ", $message);
	$command = array_shift($params);

	switch ($command) {
	    case "help":
		$this->irc->sendPrivateMessage($to, "Available commands: skip, res, players, kick [login], ignore [login], unignore [login]");
		break;
	    case "kick":
		$this->kick($params, $to);
		break;
	    case "ignore":
		$this->ignore($params, $to);
		break;
	    case "unignore":
		$this->unignore($params, $to);
		break;
	    case "skip":
		$this->skip($to);
		break;
	    case "res":
		$this->res($to);
		break;
	    case "players":
		$this->players($to);
		break;
	    default:
		break;
	}
    }

    private function kick($login, $to) {
	try {
	    $this->connection->kick($login);
	    $this->irc->sendPrivateMessage($to, "Kicked " . $params[0]);
	} catch (\Exception $e) {
	    $this->irc->sendPrivateMessage($to, "Failed to kick:" . $e->getMessage());
	}
    }

    private function ignore($login, $to) {
	try {
	    $this->connection->ignore($login);
	    $this->connection->chatSendServerMessage($login . " is now ignored from the chat!");
	    $this->connection->chatSendServerMessage("You are not allowed to chat anymore!", $login);

	    $this->irc->sendPrivateMessage($to, "Ignored: " . $login);
	} catch (\Exception $e) {
	    $this->irc->sendPrivateMessage($to, "Failed to ignore: " . $e->getMessage());
	}
    }

    private function unignore($login, $to) {
	try {
	    $this->connection->unIgnore($login);
	    $this->connection->chatSendServerMessage($login . " is unignored from the chat!");
	    $this->connection->chatSendServerMessage("You are allowed to chat again!", $login);
	    $this->irc->sendPrivateMessage($to, "Unignored: " . $login);
	} catch (\Exception $e) {
	    $this->irc->sendPrivateMessage($to, "Failed to unignore:" . $e->getMessage());
	}
    }

    private function skip($to) {
	try {
	    $this->connection->nextMap($this->storage->gameInfos->gameMode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP);
	    $this->irc->sendPrivateMessage($to, "Skipped.");
	} catch (\Exception $e) {
	    $this->irc->sendPrivateMessage($to, "Failed to skip: " . $e->getMessage());
	}
    }

    private function res($login, $to) {
	try {
	    $this->connection->restartMap($this->storage->gameInfos->gameMode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP);
	    $this->irc->sendPrivateMessage($to, "Restarted.");
	} catch (\Exception $e) {
	    $this->irc->sendPrivateMessage($to, "Failed to restart: " . $e->getMessage());
	}
    }

    private function players($to) {
	$pla = "Players:  ";	
	$placount = 0;
	$speccount = 0;
	foreach ($this->storage->players as $login => $player) {
	    $placount++;
	    $pla .= \ManiaLib\Utils\Formatting::stripStyles($player->nickName) . " ($login) |  ";
	}
	$spec = "Spectators:  ";
	foreach ($this->storage->spectators as $login => $player) {
	    $speccount++;
	    $spec .= \ManiaLib\Utils\Formatting::stripStyles($player->nickName) . " ($login) |  ";
	}
	$this->irc->sendPrivateMessage($to, "Players at server: ". $placount . " (spec: $speccount)");
	$this->irc->sendPrivateMessage($to, $pla);
	$this->irc->sendPrivateMessage($to, $spec);
	
    }

    public function irc_onPublicChat($connection, $channel, $nick, $message) {
	
    }

}
