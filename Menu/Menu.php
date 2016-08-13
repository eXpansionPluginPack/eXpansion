<?php

namespace ManiaLivePlugins\eXpansion\Menu;

use Exception;
use ManiaLive\Event\Dispatcher;
use ManiaLive\Gui\Group;
use ManiaLive\Utilities\Logger;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Events\Event;
use ManiaLivePlugins\eXpansion\AdminGroups\Events\Listener;
use ManiaLivePlugins\eXpansion\AdminGroups\Group as AdmGroup;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Menu\Gui\Widgets\MenuWidget;

/**
 * Description of Menu2
 * Second attempt to create (optimized) menu
 *
 * @author Petri Järvisalo <petri.jarvisalo@gmail.com>
 */
class Menu extends ExpPlugin implements Listener
{
    /**
     *
     * @var Group[]
     */
    protected $menuGroups = [];
    protected $menuWindows = [];

    public function eXpOnReady()
    {
        $this->enablePluginEvents();
        Dispatcher::register(Event::getClass(), $this);
        $this->prepareMenu();
        $this->enableDedicatedEvents();
    }

    public function eXpAdminAdded($login)
    {
        $name = AdminGroups::getGroupName($login);
        $this->menuGroups['Guest']->remove($login);

        if (!array_key_exists($name, $this->menuGroups)) {
            $group = AdminGroups::getAdmin($login)->getGroup();

            $this->menuGroups[$group->getGroupName()] = Group::Create($group->getGroupName());
            foreach ($group->getGroupUsers() as $user) {
                $this->menuGroups[$group->getGroupName()]->add($user->getLogin());
            }
            $this->createMenu($group);
        } else {
            $this->menuGroups[$name]->add((string)$login, true);
        }
    }

    public function eXpAdminRemoved($login)
    {
        foreach ($this->menuGroups as $name => $group) {
            if ($group->contains($login)) {
                $this->menuGroups[$name]->remove($login);
            }
        }
        $this->menuGroups['Guest']->add((string)$login, true);
    }

    public function onPlayerConnect($login, $isSpectator)
    {
        $name = AdminGroups::getGroupName($login);
        if (!array_key_exists($name, $this->menuGroups)) {
            $this->createMenu($name);
            $this->menuGroups[$name]->add((string)$login, true);
        } else {
            $this->menuGroups[$name]->add((string)$login, true);
        }

        $this->menuWindows[$name]->show((string)$login);
        
    }

    public function onPlayerDisconnect($login, $disconnectionReason)
    {
        $name = AdminGroups::getGroupName($login);
        if (array_key_exists($name, $this->menuGroups)) {
            $this->menuGroups[$name]->remove($login);
        }
    }

    public function onPluginLoaded($pluginId)
    {
        $this->prepareMenu();
    }

    public function onPluginUnloaded($pluginId)
    {
        $this->prepareMenu();
    }

    public function prepareMenu()
    {
        $this->menuGroups = [];
        MenuWidget::EraseAll();

        foreach (AdminGroups::getGroupList() as $group) {
            $this->menuGroups[$group->getGroupName()] = Group::Create($group->getGroupName());
            foreach ($group->getGroupUsers() as $user) {
                $this->menuGroups[$group->getGroupName()]->add($user->getLogin());
            }
            $this->createMenu($group);
        }

        $players = $this->storage->players + $this->storage->spectators;
        $regularPlayers = [];

        $admins = AdminGroups::get();

        foreach ($players as $login => $player) {
            if (!in_array($login, $admins)) {
                $regularPlayers[] = (string)$login;
            }
        }

        $this->menuGroups['Guest'] = Group::Create('Guest', $regularPlayers);
        $this->createMenu(new AdmGroup('Guest', false));
    }

    public function createMenu(AdmGroup $group)
    {
        $menu = MenuWidget::Create($this->menuGroups[$group->getGroupName()], false);
        if ($this->pluginLoaded("Faq")) {
            $menu->addItem("Help", "!help", $this);
        }

        if ($this->pluginLoaded("Players")) {
            $menu->addItem("Players", "!players", $this);
        }

        // Maps
        $mapsGroup = $menu->addGroup("Maps");

        if ($this->pluginLoaded("Maps")) {
            $mapsGroup->addItem("Show Maps", "!maplist", $this);
        }
        if ($group->hasPermission(Permission::MAP_ADD_LOCAL)) {
            if ($this->pluginLoaded("Maps")) {
                $mapsGroup->addItem("Add Local Maps", "!addMaps", $this);
            }
        }
        if ($group->hasPermission(Permission::MAP_ADD_MX)) {
            if ($this->pluginLoaded("ManiaExchange")) {
                $mapsGroup->addItem("ManiaExchange", "!mx", $this);
            }
        }
        if ($group->hasPermission(Permission::MAP_REMOVE_MAP)) {
            if ($this->pluginLoaded("Maps")) {
                $mapsGroup->addItem('$f00Remove this', "!admremovemap", $this);
            }
            if ($this->pluginLoaded("Maps")) {
                $mapsGroup->addItem('$f00Trash this', "!admtrashmap", $this);
            }
        }
        // records
        $recGroup = $menu->addGroup("Records");
        if ($this->pluginLoaded("Dedimania") || $this->pluginLoaded("Dedimania_Script")) {
            $recGroup->addItem("Dedimania", "!dedirecs", $this);
        }

        if ($this->pluginLoaded("LocalRecords")) {
            $recGroup->addItem("Local", "!showrecs", $this);
            $recGroup->addItem("Hall of Fame", "!topsums", $this);
            $recGroup->addItem("Server Ranks", "!serverranks", $this);
        }

        // statistics
        if ($this->pluginLoaded("Statistics")) {
            $menu->addItem("Statistics", "!stats", $this);
        }

        // Vote
        $voteGroup = $menu->addGroup("Vote");
        $voteGroup->addItem("Skip", "!voteskip", $this);
        $voteGroup->addItem("Res", "!voteres", $this);
        if ($group->hasPermission(Permission::SERVER_VOTES)) {
            $voteGroup->addItem("Config...", "!adm_votes", $this);
            $voteGroup->addItem('$f00Cancel', "!admcancel", $this);
        }

        $hudGroup = $menu->addGroup("Hud");
        $hudGroup->addItem("Move", "!hudMove", $this);
        $hudGroup->addItem("Lock", "!hudLock", $this);
        $hudGroup->addItem("Reset", "!hudReset", $this);
        $hudGroup->addItem("Config...", "!hudConfig", $this);

        // admin

        if ($group->hasPermission(Permission::TEAM_BALANCE) || $group->hasPermission(Permission::MAP_END_ROUND) || $group->hasPermission(Permission::MAP_RES) || $group->hasPermission(Permission::MAP_SKIP)) {
            $admGroup = $menu->addGroup('$f00Admin');

            if ($group->hasPermission(Permission::MAP_RES)) {
                $admGroup->addItem("Instant Res", "!admres", $this);
            }

            if ($group->hasPermission(Permission::MAP_RES)) {
                $admGroup->addItem("Replay", "!admreplay", $this);
            }

            if ($group->hasPermission(Permission::MAP_SKIP)) {
                $admGroup->addItem("Skip", "!admskip", $this);
            }

            if ($group->hasPermission(Permission::MAP_END_ROUND)) {
                $admGroup->addItem("End Round", "!admer", $this);
            }

            if ($group->hasPermission(Permission::TEAM_BALANCE)) {
                $admGroup->addItem("Balance teams", "!teambalance", $this);
            }
        }

        if ($group->hasPermission(Permission::SERVER_CONTROL_PANEL)) {
            $serverGroup = $menu->addGroup("Server Control");
            $serverGroup->addItem('Control Panel', "!admcontrol", $this);
            if ($group->hasPermission(Permission::EXPANSION_PLUGIN_SETTINGS)) {
                $serverGroup->addItem('$fffe$3afX$fffpansion Config', "!adm_settings", $this);
            }
            if ($group->hasPermission(Permission::EXPANSION_PLUGIN_START_STOP)) {
                $serverGroup->addItem("Plugin Manager", "!adm_plugins", $this);
            }
        }

        $menu->addItem("Server Info", "!serverinfo", $this);
        $this->menuWindows[$group->getGroupName()] = $menu;
        $this->menuWindows[$group->getGroupName()]->show();
    }

    public function pluginLoaded($plugin)
    {
        return $this->isPluginLoaded($this->getPluginClass($plugin));
    }

    public function actionHandler($login, $action, $entries = [])
    {
        $adminGrp = AdminGroups::getInstance();
        try {
            switch ($action) {
                case "!maplist":
                    $this->callPublicMethod($this->getPluginClass("Maps"), "showMapList", $login);
                    break;
                case "!addMaps":
                    $this->callPublicMethod($this->getPluginClass("Maps"), "addMaps", $login);
                    break;
                case "!players":
                    $this->callPublicMethod($this->getPluginClass("Players"), "showPlayerList", $login);
                    break;
                case "!showrecs":
                    $this->callPublicMethod($this->getPluginClass("LocalRecords"), "showRecsWindow", $login, null);
                    break;
                case "!admres":
                    $adminGrp->adminCmd($login, "restart");
                    break;
                case "!admskip":
                    $adminGrp->adminCmd($login, "skip");
                    break;
                case "!admer":
                    $adminGrp->adminCmd($login, "er");
                    break;
                case "!admcancel":
                    $adminGrp->adminCmd($login, "cancel");
                    break;
                case "!admremovemap":
                    $adminGrp->adminCmd($login, "remove this");
                    break;
                case "!admtrashmap":
                    $adminGrp->adminCmd($login, "trash this");
                    break;
                case "!admmx":
                    $this->callPublicMethod($this->getPluginClass("ManiaExchange"), "mxSearch", $login, "", "");
                    break;
                case "!admcontrol":
                    $this->callPublicMethod($this->getPluginClass("Adm"), "serverControlMain", $login);
                    break;
                case "!help":
                    $this->callPublicMethod($this->getPluginClass("Faq"), "showFaq", $login, "toc", null);
                    break;
                case "!hudMove":
                    $this->callPublicMethod($this->getPluginClass("Gui"), "hudCommands", $login, "move");
                    break;
                case "!hudLock":
                    $this->callPublicMethod($this->getPluginClass("Gui"), "hudCommands", $login, "lock");
                    break;
                case "!hudConfig":
                    $this->callPublicMethod($this->getPluginClass("Gui"), "showConfigWindow", $login, $entries);
                    break;
                case "!hudReset":
                    $this->callPublicMethod($this->getPluginClass("Gui"), "hudCommands", $login, "reset");
                    break;
                case "!stats":
                    $this->callPublicMethod($this->getPluginClass("Statistics"), "showTopWinners", $login);
                    break;
                case "!serverinfo":
                    $this->callPublicMethod($this->getPluginClass("Core"), "showInfo", $login);
                    break;
                case "!serverranks":
                    $this->callPublicMethod($this->getPluginClass("LocalRecords"), "showRanksWindow", $login);
                    break;
                case "!topsums":
                    $this->callPublicMethod($this->getPluginClass("LocalRecords"), "showTopSums", $login);
                    break;
                case "!admreplay":
                    $adminGrp->adminCmd($login, "replay");
                    break;
                case "!teambalance":
                    $adminGrp->adminCmd($login, "setTeamBalance");
                    break;

                case "!adm_plugins":
                    $adminGrp->adminCmd($login, "plugins");
                    break;
                case "!adm_settings":
                    $adminGrp->adminCmd($login, "setexp");
                    break;
                case "!adm_votes":
                    $adminGrp->adminCmd($login, "votes");
                    break;
                case "!adm_groups":
                    $adminGrp->adminCmd($login, "groups");
                    break;
                case "!adm_update":
                    $adminGrp->adminCmd($login, "update");
                    break;
                case "!mx":
                    $this->callPublicMethod($this->getPluginClass("ManiaExchange"), "mxSearch", $login, "", "");
                    break;
                case "!localcps":
                    $this->callPublicMethod($this->getPluginClass("LocalRecords"), "showCpWindow", $login);
                    break;
                case "!dedicps":
                    $plugin = $this->getPluginClass("Dedimania");
                    if ($this->isPluginLoaded($plugin)) {
                        $this->callPublicMethod($plugin, "showCps", $login);
                    }
                    $plugin = $this->getPluginClass("Dedimania_Script");
                    if ($this->isPluginLoaded($plugin)) {
                        $this->callPublicMethod($plugin, "showCps", $login);
                    }
                    break;
                case "!dedirecs":
                    $plugin = $this->getPluginClass("Dedimania");
                    if ($this->isPluginLoaded($plugin)) {
                        $this->callPublicMethod($plugin, "showRecs", $login);
                    }
                    $plugin = $this->getPluginClass("Dedimania_Script");
                    if ($this->isPluginLoaded($plugin)) {
                        $this->callPublicMethod($plugin, "showRecs", $login);
                    }
                    break;
                case "!voteres":
                    $plugin = $this->getPluginClass("Votes");
                    if ($this->isPluginLoaded($plugin)) {
                        $this->callPublicMethod($plugin, "vote_restart", $login);
                    } else {
                        $this->connection->callVoteRestartMap();
                    }
                    break;
                case "!voteskip":
                    $plugin = $this->getPluginClass("Votes");
                    if ($this->isPluginLoaded($plugin)) {
                        $this->callPublicMethod($plugin, "vote_skip", $login);
                    } else {
                        $this->connection->callVoteNextMap();
                    }
                    break;
                default:
                    $this->eXpChatSendServerMessage("not found: " . $action, $login);
                    Logger::info("menu command not found: " . $action);
                    break;
            }
        } catch (Exception $ex) {
            Logger::error("Error in Menu while running action : " . $action);
        }
    }

    /**
     *
     * @param string $plugin
     *
     * @return string
     */
    private function getPluginClass($plugin)
    {
        return "\\ManiaLivePlugins\\eXpansion\\" . $plugin . "\\" . $plugin;
    }
}