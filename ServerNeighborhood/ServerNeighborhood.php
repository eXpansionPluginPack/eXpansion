<?php

/**
 *
 * @name Oliverde8 Server Switch
 * @date      23-03-2013
 * @version   1.0
 * @website   oliver-decramer.com
 * @package   oliverd8
 *
 * @author    Oliver "oliverde8" De Cramer <oliverde8@gmail.com>
 * @Idea      undef.de
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
 * You are allowed to change things of use this in other projects, as
 * long as you leave the information at the top (name, date, version,
 * website, package, author, copyright) and publish the code under
 * the GNU General Public License version 3.
 * ---------------------------------------------------------------------
 */

namespace ManiaLivePlugins\eXpansion\ServerNeighborhood;

use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Core\types\config\Variable;
use ManiaLivePlugins\eXpansion\ServerNeighborhood\Gui\Widgets\ServerPanel;
use ManiaLivePlugins\eXpansion\ServerNeighborhood\Gui\Windows\PlayerList;
use ManiaLivePlugins\eXpansion\ServerNeighborhood\Gui\Windows\ServerList;

class ServerNeighborhood extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    public static $gamemodes = array(
        0 => array('name' => 'SCRIPT', 'icon' => 'RT_Script'),
        1 => array('name' => 'ROUNDS', 'icon' => 'RT_Rounds'),
        2 => array('name' => 'TIME_ATTACK', 'icon' => 'RT_TimeAttack'),
        3 => array('name' => 'TEAM', 'icon' => 'RT_Team'),
        4 => array('name' => 'LAPS', 'icon' => 'RT_Laps'),
        5 => array('name' => 'CUP', 'icon' => 'RT_Cup'),
        6 => array('name' => 'STUNTS', 'icon' => 'RT_Stunts'),
    );

    private $server;

    private $servers = array();

    private $lastSent = 0;

    private $config;

    public function eXpOnInit()
    {
        $this->setVersion("1.0");
        $this->config = Config::getInstance();
    }

    public function eXpOnReady()
    {
        $this->server = new Server();
        $this->server->create_fromConnection($this->connection, $this->storage);

        $this->registerChatCommand('servers', 'showServerList', 0, true);

        $this->enableTickerEvent();
    }

    public function onSettingsChanged(Variable $var)
    {
        if ($var->getName() == 'storing_path') {
            $status = $this->saveData($this->server->createXML($this->connection, $this->storage));
            $this->lastSent = time();

            if (!$status) {
                $admins = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance();
                $admins->announceToPermission(
                    Permission::EXPANSION_PLUGIN_SETTINGS,
                    "#admin_error#[ServerNeighborhoo]Storage path is wrong. Can't write!!"
                );
            }
        }

        if ($var->getName() == 'snwidget_isDockable') {
            $this->getData();
            ServerPanel::EraseAll();
            $this->showWidget($this->servers);
            $this->lastSent = time();
        }
    }

    public function onTick()
    {
        parent::onTick();
        if ((time() - $this->lastSent) > Config::getInstance()->refresh_interval) {
            $this->saveData($this->server->createXML($this->connection, $this->storage));
            $this->lastSent = time();

            if (sizeof($this->storage->players) > 0 || sizeof($this->storage->spectators) > 0) {
                $this->getData();
                $this->showWidget($this->servers);
            }
        }
    }

    public function saveData($data)
    {
        $filename = Config::getInstance()->storing_path . $this->storage->serverLogin . '_serverinfo.xml';

        // Opens the file for writing and truncates it to zero length
        // Try min. 40 times to open if it fails (write block)
        $tries = 0;

        try {
            $fh = fopen($filename, "w", 0, stream_context_create(array('ftp' => array('overwrite' => true))));
        } catch (\Exception $ex) {
            $fh = false;
        }
        while ($fh === false) {
            if ($tries > 40) {
                break;
            }
            $tries++;
            try {
                $fh = fopen($filename, "w", 0, $this->stream_context);
            } catch (\Exception $ex) {
                $fh = false;
            }
        }
        if ($tries >= 40) {
            $this->console(
                '[server_neighborhood] Could not open file " '
                . $filename . '" to store the Server Information!'
            );

            return false;
        } else {
            fwrite($fh, $data);
            fclose($fh);
        }

        return true;
    }

    public function getData()
    {

        $i = 0;
        foreach ($this->config->servers as $serverPath) {

            try {
                $data = file_get_contents($serverPath);

                if (isset($this->servers[$i]) && is_object($this->servers[$i])) {
                    $server = $this->servers[$i];
                } else {
                    $server = new Server();
                    $this->servers[$i] = $server;
                }
                $xml = simplexml_load_string($data);

                if (!$xml) {
                    \ManiaLive\Utilities\Console::println(
                        '[server_neighborhood] Error loading : '
                        . $serverPath . ' invalid XML?'
                    );
                } else {
                    $server->setServer_data($xml);
                }
                $i++;
            } catch (\Exception $ex) {
                \ManiaLive\Utilities\Console::println('[server_neighborhood] Error loading : ' . $serverPath);
            }
        }
    }

    public function showWidget($servers)
    {
        $windows = ServerPanel::GetAll();
        if (empty($windows)) {
            $window = ServerPanel::Create(null);
            $windows[] = $window;
            $window->setSize(33, 25);
            $window->setPosZ(50);
            $window->setPosition(-160, -20);
            $window->update($servers);
            $window->show();
        } else {
            foreach ($windows as $window) {
                $window->redraw();
            }
        }
    }

    public function showServerList($login)
    {
        ServerList::Erase($login);
        $w = ServerList::Create($login);
        $w->setTitle('ServerNeighborhood - Server List');
        $w->setSize(120, 105);
        $w->setServers($this->servers);
        $w->centerOnScreen();
        $w->show();
    }

    public function eXpOnUnload()
    {
        ServerPanel::EraseAll();
        ServerList::EraseAll();
        PlayerList::EraseAll();
    }
}
