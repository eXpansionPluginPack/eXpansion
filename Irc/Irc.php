<?php

/*
 * ---------------------------------------------------------------------
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
 * ---------------------------------------------------------------------
 * You are allowed to change things or use this in other projects, as
 * long as you leave the information at the top (name, date, version,
 * website, package, author, copyright) and publish the code under
 * the GNU General Public License version 3.
 * ---------------------------------------------------------------------
 */

namespace ManiaLivePlugins\eXpansion\Irc;

use ManiaLive\Utilities\Console;

class Irc extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin implements Classes\IrcListener {

    /** @var Classes\IrcConnection */
    private $irc;

    /** @var Config */
    private $config;

    function exp_onLoad() {
	$this->enableDedicatedEvents();
	$this->enableApplicationEvents();
	$this->enableTickerEvent();
	$this->irc = Classes\IrcConnection::getInstance();
	$this->config = \ManiaLivePlugins\eXpansion\Irc\Config::getInstance();

	$ircConfig = new Classes\IrcConfig();
	$ircConfig->server = $this->config->server;
	$ircConfig->port = $this->config->port;
	$ircConfig->channel = $this->config->channel;
	$ircConfig->nickname = $this->config->nickname;
	$ircConfig->realname = $this->config->realname;

	try {
	    $this->irc->connect($ircConfig);
	    $this->irc->registerCallbackClass($this);
	} catch (\Exception $e) {
	    $this->dumpException("An error has occurred while connecting to Irc", $e);
	}
    }

    function onPreLoop() {
	$this->irc->onTick();
    }

    public function exp_unload() {
	$this->irc->disconnect();
	parent::exp_unload();
    }

    public function irc_onConnect($connection) {
	
    }

    public function irc_onDisconnect() {
	
    }

    public function irc_onPrivateMessage($connection, $nick, $message) {
	
    }

    public function irc_onPublicChat($connection, $channel, $nick, $message) {
	if (substr($message, 0, 1) != "!") {
	    $this->connection->chatSendServerMessage('$fff($f00Irc$fff) $fff' . $connection->getIrcNick($nick) . ': $ff0' . $message);
	}
    }

    public function onPlayerConnect($login, $isSpectator) {
	$player = $this->storage->getPlayerObject($login);
	$nick = $player->nickName;
	$message = "Player " . \ManiaLib\Utils\Formatting::stripStyles($nick) . " (" . $login . ") Connected from " . $player->path;
	$this->irc->sendChat($message);
    }

    public function onPlayerDisconnect($login, $disconnectionReason = null) {
	$player = $this->storage->getPlayerObject($login);
	$nick = $player->nickName;
	$message = "Player " . \ManiaLib\Utils\Formatting::stripStyles($nick) . " (" . $login . ") Leaves the server.";
	$this->irc->sendChat($message);
    }

    public function onPlayerChat($playerUid, $login, $text, $isRegistredCmd) {
	if ($playerUid != 0 && substr($text, 0, 1) != "/") {
	    $nick = $this->storage->getPlayerObject($login);
	    $nick = $nick->nickName;
	    $message = \ManiaLib\Utils\Formatting::stripStyles($nick) . ":" . \ManiaLib\Utils\Formatting::stripStyles($text);
	    $this->irc->sendChat($message);
	}
    }

}

?>