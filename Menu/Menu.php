<?php

namespace ManiaLivePlugins\eXpansion\Menu;

use ManiaLive\Event\Dispatcher;
use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Events\Event;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Menu\Gui\Widgets\Submenu;

class Menu extends ExpPlugin
{
    private $doCheck = false;
    private $counter = 0;
    private $actions = array();

    function exp_onReady()
    {
        $this->enableTickerEvent();
        $this->enableDedicatedEvents();
        $this->enablePluginEvents();
        if ($this->exp_isPluginLoaded("AdminGroups")) {
            Dispatcher::register(Event::getClass(), $this);
        }

        $actionHandler = ActionHandler::getInstance();

        $this->actions['playerlist']   = $actionHandler->createAction(array($this, "actions"), "playerlist");
        $this->actions['maplist']      = $actionHandler->createAction(array($this, "actions"), "maplist");
        $this->actions['maprecords']   = $actionHandler->createAction(array($this, "actions"), "maprecords");
        $this->actions['addMaps']      = $actionHandler->createAction(array($this, "actions"), "addMaps");
        $this->actions['voteres']      = $actionHandler->createAction(array($this, "actions"), "voteres");
        $this->actions['voteskip']     = $actionHandler->createAction(array($this, "actions"), "voteskip");
        $this->actions['admres']       = $actionHandler->createAction(array($this, "actions"), "admres");
        $this->actions['admskip']      = $actionHandler->createAction(array($this, "actions"), "admskip");
        $this->actions['admer']        = $actionHandler->createAction(array($this, "actions"), "admer");
        $this->actions['admcancel']    = $actionHandler->createAction(array($this, "actions"), "admcancel");
        $this->actions['admremovemap'] = $actionHandler->createAction(array($this, "actions"), "admremovemap");
        $this->actions['admtrashmap']  = $actionHandler->createAction(array($this, "actions"), "admtrashmap");
        $this->actions['admmx']        = $actionHandler->createAction(array($this, "actions"), "admmx");
        $this->actions['admcontrol']   = $actionHandler->createAction(array($this, "actions"), "admcontrol");
        $this->actions['quit']         = $actionHandler->createAction(array($this, "actions"), "quit");
        $this->actions['help']         = $actionHandler->createAction(array($this, "actions"), "help");
        $this->actions['hudMove']      = $actionHandler->createAction(array($this, "actions"), "hudMove");
        $this->actions['hudLock']      = $actionHandler->createAction(array($this, "actions"), "hudLock");
        $this->actions['hudConfig']    = $actionHandler->createAction(array($this, "actions"), "hudConfig");
        $this->actions['hudReset']     = $actionHandler->createAction(array($this, "actions"), "hudReset");
        $this->actions['stats']        = $actionHandler->createAction(array($this, "actions"), "stats");
        $this->actions['serverinfo']   = $actionHandler->createAction(array($this, "actions"), "serverinfo");
        $this->actions['admreplay']    = $actionHandler->createAction(array($this, "actions"), "admreplay");
        $this->actions['serverranks']  = $actionHandler->createAction(array($this, "actions"), "serverranks");
        $this->actions['topsums']      = $actionHandler->createAction(array($this, "actions"), "topsums");
        $this->actions['teambalance']  = $actionHandler->createAction(array($this, "actions"), "teambalance");
        $this->actions['localcps']     = $actionHandler->createAction(array($this, "actions"), "localcps");
        $this->actions['dedicps']      = $actionHandler->createAction(array($this, "actions"), "dedicps");
        $this->actions['dedirecs']     = $actionHandler->createAction(array($this, "actions"), "dedirecs");


        $this->actions['adm_groups']   = $actionHandler->createAction(array($this, "actions"), "adm_groups");
        $this->actions['adm_plugins']  = $actionHandler->createAction(array($this, "actions"), "adm_plugins");
        $this->actions['adm_votes']    = $actionHandler->createAction(array($this, "actions"), "adm_votes");
        $this->actions['adm_settings'] = $actionHandler->createAction(array($this, "actions"), "adm_settings");
        $this->actions['adm_update']   = $actionHandler->createAction(array($this, "actions"), "adm_update");
    }

    private function getPluginName($plugin)
    {
        return "\\ManiaLivePlugins\\eXpansion\\".$plugin."\\".$plugin;
    }

    private function exp_isPluginLoaded($plugin)
    {
        return $this->isPluginLoaded($this->getPluginName($plugin));
    }

    function actions($login, $action, $entries)
    {
        $adminGrp = AdminGroups::getInstance();

        try {
            switch ($action) {
                case "playerlist":
                    $this->callPublicMethod($this->getPluginName("Players"), "showPlayerList", $login);
                    break;
                case "maplist":
                    $this->callPublicMethod($this->getPluginName("Maps"), "showMapList", $login);
                    break;
                case "addMaps":
                    $this->callPublicMethod($this->getPluginName("Maps"), "addMaps", $login);
                    break;
                case "maprecords":
                    $this->callPublicMethod($this->getPluginName("LocalRecords"), "showRecsWindow", $login, Null);
                    break;
                case "voteres":
                    $plugin = $this->getPluginName("Votes");
                    if ($this->isPluginLoaded($plugin)) {
                        $this->callPublicMethod($plugin, "vote_restart", $login);
                    } else {
                        $this->connection->callVoteRestartMap();
                    }
                    break;
                case "voteskip":
                    $plugin = $this->getPluginName("Votes");
                    if ($this->isPluginLoaded($plugin)) {
                        $this->callPublicMethod($plugin, "vote_skip", $login);
                    } else {
                        $this->connection->callVoteNextMap();
                    }
                    break;
                case "quit":
                    $this->connection->kick($login, "Thanks for visiting and welcome back");
                    break;
                case "admres":
                    $adminGrp->adminCmd($login, "restart");
                    break;
                case "admskip":
                    $adminGrp->adminCmd($login, "skip");
                    break;
                case "admer":
                    $adminGrp->adminCmd($login, "er");
                    break;
                case "admcancel":
                    $adminGrp->adminCmd($login, "cancel");
                    break;
                case "admremovemap":
                    $adminGrp->adminCmd($login, "remove this");
                    break;
                case "admtrashmap":
                    $adminGrp->adminCmd($login, "trash this");
                    break;
                case "admmx":
                    $this->callPublicMethod($this->getPluginName("ManiaExchange"), "mxSearch", $login, "", "");
                    break;
                case "admcontrol":
                    $this->callPublicMethod($this->getPluginName("Adm"), "serverControlMain", $login);
                    break;
                case "help":
                    $this->callPublicMethod($this->getPluginName("Faq"), "showFaq", $login, "toc", null);
                    break;
                case "hudMove":
                    $this->callPublicMethod($this->getPluginName("Gui"), "hudCommands", $login, "move");
                    break;
                case "hudLock":
                    $this->callPublicMethod($this->getPluginName("Gui"), "hudCommands", $login, "lock");
                    break;
                case "hudConfig":
                    $this->callPublicMethod($this->getPluginName("Gui"), "showConfigWindow", $login, $entries);
                    break;
                case "hudReset":
                    $this->callPublicMethod($this->getPluginName("Gui"), "hudCommands", $login, "reset");
                    break;
                case "stats":
                    $this->callPublicMethod($this->getPluginName("Statistics"), "showTopWinners", $login);
                    break;
                case "serverinfo":
                    $this->callPublicMethod($this->getPluginName("Core"), "showInfo", $login);
                    break;
                case "serverranks":
                    $this->callPublicMethod($this->getPluginName("LocalRecords"), "showRanksWindow", $login);
                    break;
                case "topsums":
                    $this->callPublicMethod($this->getPluginName("LocalRecords"), "showTopSums", $login);
                    break;
                case "admreplay":
                    $adminGrp->adminCmd($login, "replay");
                    break;
                case "teambalance":
                    $adminGrp->adminCmd($login, "setTeamBalance");
                    break;

                case "adm_plugins":
                    $adminGrp->adminCmd($login, "plugins");
                    break;
                case "adm_settings":
                    $adminGrp->adminCmd($login, "setexp");
                    break;
                case "adm_votes":
                    $adminGrp->adminCmd($login, "votes");
                    break;
                case "adm_groups":
                    $adminGrp->adminCmd($login, "groups");
                    break;
                case "adm_update":
                    $adminGrp->adminCmd($login, "update");
                    break;

                case "localcps":
                    $this->callPublicMethod($this->getPluginName("LocalRecords"), "showCpWindow", $login);
                    break;
                case "dedicps":
                    $plugin = $this->getPluginName("Dedimania");
                    if ($this->isPluginLoaded($plugin)) {
                        $this->callPublicMethod($plugin, "showCps", $login);
                    }
                    $plugin = $this->getPluginName("Dedimania_Script");
                    if ($this->isPluginLoaded($plugin)) {
                        $this->callPublicMethod($plugin, "showCps", $login);
                    }
                    break;
                case "dedirecs":
                    $plugin = $this->getPluginName("Dedimania");
                    if ($this->isPluginLoaded($plugin)) {
                        $this->callPublicMethod($plugin, "showRecs", $login);
                    }
                    $plugin = $this->getPluginName("Dedimania_Script");
                    if ($this->isPluginLoaded($plugin)) {
                        $this->callPublicMethod($plugin, "showRecs", $login);
                    }
                    break;
            }
        } catch (Exception $e) {
            Logger::error("Error in Menu while running action : ".$action);
        }
    }

    function exp_admin_added($login)
    {
        Submenu::Erase($login);
        $this->onPlayerConnect($login, false);
    }

    function exp_admin_removed($login)
    {
        Submenu::Erase($login);
        $this->onPlayerConnect($login, false);
    }

    function onPlayerConnect($login, $isSpectator)
    {

        $submenu = Submenu::Create($login);
        $menu    = $submenu->getMenu();

        if ($this->exp_isPluginLoaded('Faq')) {
            $submenu->addItem($menu, __("Help", $login), $this->actions['help']);
        }

        if ($this->exp_isPluginLoaded("Players")) {
            $submenu->addItem($menu, __("Players", $login), $this->actions['playerlist']);
        }

        $mapAction = null;
        $mapText   = "";
        if ($this->exp_isPluginLoaded("Maps")) {
            $maps = $submenu->addSubMenu($menu, __("Maps", $login));

            $submenu->addItem($maps, __("Show Maps", $login), $this->actions['maplist']);

            if ($this->isPluginLoaded("\\ManiaLivePlugins\\eXpansion\\Maps\\Maps") && AdminGroups::hasPermission($login,
                    Permission::map_addLocal) || AdminGroups::hasPermission($login, Permission::map_addMX) || AdminGroups::hasPermission($login,
                    Permission::map_removeMap)) {
                if (AdminGroups::hasPermission($login, Permission::map_addLocal)) {
                    if ($this->expStorage->isRemoteControlled == false) {
                        $submenu->addItem($maps, __("Add local map", $login), $this->actions['addMaps']);
                    }
                }
                if ($this->isPluginLoaded("\\ManiaLivePlugins\\eXpansion\\ManiaExchange\\ManiaExchange") && AdminGroups::hasPermission($login,
                        Permission::map_addMX)) {
                    $submenu->addItem($maps, __("Mania-Exchange", $login), $this->actions['admmx']);
                }

                if (AdminGroups::hasPermission($login, Permission::map_removeMap)) {
                    $submenu->addItem($maps, "", null);
                    $submenu->addItem($maps, __("Remove this", $login), $this->actions['admremovemap']);
                    $submenu->addItem($maps, __("Trash this", $login), $this->actions['admtrashmap']);
                }
            }
        }

        if ($this->exp_isPluginLoaded("LocalRecords") || $this->exp_isPluginLoaded("Dedimania") || $this->exp_isPluginLoaded("Dedimania_Script")) {
            $records = $submenu->addSubMenu($menu, __("Records", $login), null);

            if ($this->exp_isPluginLoaded("Dedimania") || $this->exp_isPluginLoaded("Dedimania_Script")) {
                $submenu->addItem($records, __("Dedimania", $login), $this->actions['dedirecs']);
            }

            if ($this->exp_isPluginLoaded("LocalRecords")) {
                $submenu->addItem($records, __("Local Records", $login), $this->actions['maprecords']);
                $submenu->addItem($records, __("Hall of Fame", $login), $this->actions['topsums']);
                $submenu->addItem($records, __("Server Ranks", $login), $this->actions['serverranks']);
            }
        }


        $statAction = null;
        if ($this->exp_isPluginLoaded("Statistics")) {
            $statAction = $this->actions['stats'];
            //$submenu->addItem($stats, __("Statistics...", $login), $this->actions['stats']);

            $stats = $submenu->addItem($menu, __("Statistics", $login), $statAction);
        }

        $hud = $submenu->addSubMenu($menu, __("Hud", $login));
        $submenu->addItem($hud, __("Move", $login), $this->actions['hudMove']);
        $submenu->addItem($hud, __("Lock", $login), $this->actions['hudLock']);
        $submenu->addItem($hud, __("Reset", $login), $this->actions['hudReset']);
        $submenu->addItem($hud, __("Config...", $login), $this->actions['hudConfig']);


        $votes = $submenu->addSubMenu($menu, __("Vote", $login));

        $submenu->addItem($votes, __("Restart", $login), $this->actions['voteres']);
        $submenu->addItem($votes, __("Skip", $login), $this->actions['voteskip']);

        if (AdminGroups::hasPermission($login, Permission::server_votes)) {
            $submenu->addItem($votes, "", null);
            $submenu->addItem($votes, __("Cancel", $login), $this->actions['admcancel']);
            $submenu->addItem($votes, __("Config", $login), $this->actions['adm_votes']);
        }

        if (AdminGroups::hasPermission($login, Permission::team_balance) || AdminGroups::hasPermission($login, Permission::map_endRound) || AdminGroups::hasPermission($login,
                Permission::map_restart) || AdminGroups::hasPermission($login, Permission::map_skip)) {
            $adm = $submenu->addSubMenu($menu, __("Admin", $login), null);

            if (AdminGroups::hasPermission($login, Permission::map_restart))
                    $submenu->addItem($adm, __("Instant Restart", $login), $this->actions['admres']);

            if (AdminGroups::hasPermission($login, Permission::map_restart))
                    $submenu->addItem($adm, __("Replay", $login), $this->actions['admreplay']);

            if (AdminGroups::hasPermission($login, Permission::map_skip))
                    $submenu->addItem($adm, __("Skip", $login), $this->actions['admskip']);

            if (AdminGroups::hasPermission($login, Permission::map_endRound))
                    $submenu->addItem($adm, __("End Round", $login), $this->actions['admer']);

            if (AdminGroups::hasPermission($login, Permission::team_balance))
                    $submenu->addItem($adm, __("Balance Teams", $login), $this->actions['teambalance']);
        }


        if (AdminGroups::hasPermission($login, Permission::server_controlPanel)) {
            $server = $submenu->addSubMenu($menu, __("Server Control", $login), null);
            $submenu->addItem($server, __("Settings", $login), $this->actions['admcontrol']);
            $submenu->addItem($server, "", null);
            $submenu->addItem($server, __("Plugins", $login), $this->actions['adm_plugins']);
        }

        $info = $submenu->addItem($menu, __("Server Info", $login), $this->actions['serverinfo']);
        $submenu->show();
    }

    public function onPluginLoaded($pluginId)
    {
        $this->doCheck = true;
    }

    public function onPluginUnloaded($pluginId)
    {
        $this->doCheck = true;
    }

    public function onTick()
    {
        if ($this->doCheck) {
            if ($this->counter > 1) {
                $this->counter = 0;
                $this->doCheck = false;

                Submenu::EraseAll();
                foreach ($this->storage->players as $login => $player) {
                    $this->onPlayerConnect($login, null);
                }
                foreach ($this->storage->spectators as $login => $player) {
                    $this->onPlayerConnect($login, null);
                }

                return;
            }
            $this->counter++;
        }
    }
}
?>