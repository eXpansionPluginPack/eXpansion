<?php

/**
 * eXpansion
 *
 * @name Chat
 * @date 29-01-2013
 * @version r1
 * @package eXpansion
 *
 * @author Petri Järvisalo
 * @copyright 2013
 *
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

namespace ManiaLivePlugins\eXpansion\Chat;

use ManiaLive\Utilities\Console;
use ManiaLive\Features\Admin\AdminGroup;
use ManiaLivePlugins\eXpansion\Chat\Config;

class Chat extends \ManiaLive\PluginHandler\Plugin {

    private $enabled = true;

    /**
     * onInit()
     * Function called on initialisation of ManiaLive.
     *
     * @return void
     */
    function onInit() {
        $this->setVersion(1170);
    }

    /**
     * onLoad()
     * Function called on loading of ManiaLive.
     *
     * @return void
     */
    function onLoad() {
        $this->enableDedicatedEvents();
        try {
            $this->connection->chatEnableManualRouting(true);
        } catch (\Exception $e) {
            Console::println('[eXpansion|Chat] Couldn\'t initialize chat.' . "\n" . ' Error from server: ' . $e->getMessage());
            $this->enabled = false;
        }
    }

    function onPlayerChat($playerUid, $login, $text, $isRegistredCmd) {
        if ($playerUid != 0 && substr($text, 0, 1) != "/" && $this->enabled) {
            $config = Config::getInstance();
            $source_player = $this->storage->getPlayerObject($login);
            $nick = $source_player->nickName;
            $nick = str_ireplace('$w', '', $nick);

            try {
                if (AdminGroup::contains($login)) {
                    $this->connection->chatSendServerMessage("\$fff" . $config->adminSign . " $nick\$z\$s" . $config->adminChatColor . "  " . $text);
                } elseif ($source_player->isManagedByAnOtherServer) {
                    $this->connection->chatSendServerMessage("\$fff$nick\$z\$s" . $config->otherServerChatColor . "  " . $text);
                } else {
                    $this->connection->chatSendServerMessage("\$fff$nick\$z\$s" . $config->publicChatColor . "  " . $text);
                }
            } catch (\Exception $e) {
                Console::println('[eXpansion|Chat] error sending chat from ' . $login . ': ' . $text . ' with folloing error' . "\n" . $e->getMessage());
            }
        }
    }

    /**
     * onUnload()
     * Function called on unloading this plugin.
     *
     * @return void
     */
    function onUnload() {
        $this->connection->chatEnableManualRouting(false);
        parent::onUnload();
    }

}

?>