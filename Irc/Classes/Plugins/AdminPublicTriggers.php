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
use ManiaLivePlugins\eXpansion\Irc\Config;

/**
 * Description of AdminPublic
 *
 * @author Petri
 */
class AdminPublicTriggers implements \ManiaLivePlugins\eXpansion\Irc\Classes\IrcListener
{

    /** @var IrcBot */
    private $irc;

    /** @var \Maniaplanet\DedicatedServer\Connection */
    private $connection;

    /** @var ManiaLive\Data\Storage */
    private $storage;
    private $allowedLogins = array();

    public function __construct()
    {
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \ManiaLivePlugins\eXpansion\Helpers\Singletons::getInstance()->getDediConnection();
        $this->storage = \ManiaLive\Data\Storage::getInstance();
    }

    public function irc_onConnect($connection)
    {
        $this->irc = $connection;
    }

    public function irc_onDisconnect()
    {

    }

    public function irc_onPrivateMessage($connection, $nick, $message)
    {

    }

    public function irc_onPublicChat($connection, $channel, $nick, $message)
    {

        if (substr($message, 0, 1) == "!") {
            if (!in_array($connection->getIrcNick($nick), Config::getInstance()->allowedIrcLogins)) {
                $this->irc->sendPublicChat("You are not allowed to use ! commands.");

                return;
            }
            $string = substr($message, 1);
            $params = explode(" ", $string);
            $command = array_shift($params);

            switch ($command) {
                case "kick":
                    try {
                        $this->connection->kick($params[0]);
                        $this->irc->sendPublicChat("Kicked " . $params[0]);
                    } catch (\Exception $e) {
                        $this->irc->sendPublicChat("Failed to kick:" . $e->getMessage());
                    }
            }
        }
    }
}
