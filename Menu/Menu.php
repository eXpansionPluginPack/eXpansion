<?php

namespace ManiaLivePlugins\eXpansion\Menu;

use \ManiaLive\Event\Dispatcher;
use \ManiaLivePlugins\eXpansion\Menu\Gui\Widgets\MenuPanel;
use \ManiaLivePlugins\eXpansion\Menu\Structures\Menuitem;

class Menu extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $menuItems = array();
    private $actions = array();

    function exp_onInit() {
        $this->setPublicMethod("addItem");
        $this->setPublicMethod("addSeparator");
    }

    function exp_onReady() {
        $this->enableDedicatedEvents();
        if ($this->isPluginLoaded("ManiaLivePlugins\\eXpansion\\AdminGroups\\AdminGroups")) {
            Dispatcher::register(\ManiaLivePlugins\eXpansion\AdminGroups\Events\Event::getClass(), $this);
        }

        $actionHandler = \ManiaLive\Gui\ActionHandler::getInstance();

        $this->actions['playerlist'] = $actionHandler->createAction(array($this, "actions"), "playerlist");
        $this->actions['maplist'] = $actionHandler->createAction(array($this, "actions"), "maplist");
        $this->actions['maprecords'] = $actionHandler->createAction(array($this, "actions"), "maprecords");
        $this->actions['addMaps'] = $actionHandler->createAction(array($this, "actions"), "addMaps");
        $this->actions['voteres'] = $actionHandler->createAction(array($this, "actions"), "voteres");
        $this->actions['voteskip'] = $actionHandler->createAction(array($this, "actions"), "voteskip");
        $this->actions['admres'] = $actionHandler->createAction(array($this, "actions"), "admres");
        $this->actions['admskip'] = $actionHandler->createAction(array($this, "actions"), "admskip");
        $this->actions['admer'] = $actionHandler->createAction(array($this, "actions"), "admer");
        $this->actions['admcancel'] = $actionHandler->createAction(array($this, "actions"), "admcancel");
        $this->actions['admremovemap'] = $actionHandler->createAction(array($this, "actions"), "admremovemap");
        $this->actions['admtrashmap'] = $actionHandler->createAction(array($this, "actions"), "admtrashmap");
        $this->actions['admmx'] = $actionHandler->createAction(array($this, "actions"), "admmx");
        $this->actions['admcontrol'] = $actionHandler->createAction(array($this, "actions"), "admcontrol");
        $this->actions['quit'] = $actionHandler->createAction(array($this, "actions"), "quit");
        $this->actions['help'] = $actionHandler->createAction(array($this, "actions"), "help");
        $this->actions['hudMove'] = $actionHandler->createAction(array($this, "actions"), "hudMove");
        $this->actions['hudLock'] = $actionHandler->createAction(array($this, "actions"), "hudLock");
        $this->actions['hudConfig'] = $actionHandler->createAction(array($this, "actions"), "hudConfig");
        $this->actions['hudReset'] = $actionHandler->createAction(array($this, "actions"), "hudReset");
        $this->actions['stats'] = $actionHandler->createAction(array($this, "actions"), "stats");
        $this->actions['serverinfo'] = $actionHandler->createAction(array($this, "actions"), "serverinfo");
	$this->actions['admreplay'] = $actionHandler->createAction(array($this, "actions"), "admreplay"); 
        
	foreach ($this->storage->players as $login => $player) {
	    $this->onPlayerConnect($login, null);
	}
	foreach ($this->storage->spectators as $login => $player) {
	    $this->onPlayerConnect($login, null);
	}
	
	
    }

    function actions($login, $action, $entries) {
        $adminGrp = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance();

        switch ($action) {
            case "playerlist":
                $this->callPublicMethod("ManiaLivePlugins\\eXpansion\Players\\Players", "showPlayerList", $login);
                break;
            case "maplist":
                $this->callPublicMethod("ManiaLivePlugins\\eXpansion\\Maps\\Maps", "showMapList", $login);
                break;
            case "addMaps":
                $this->callPublicMethod("ManiaLivePlugins\\eXpansion\\Maps\\Maps", "addMaps", $login);
                break;
            case "maprecords":
                $this->callPublicMethod("ManiaLivePlugins\\eXpansion\\LocalRecords\\LocalRecords", "showRecsWindow", $login, Null);
                break;
            case "voteres":
                $this->callPublicMethod("ManiaLivePlugins\\eXpansion\\Votes\Votes", "vote_restart", $login);
                break;
            case "voteskip":
                $this->callPublicMethod("ManiaLivePlugins\\eXpansion\\Votes\\Votes", "vote_skip", $login);
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
                $this->callPublicMethod("ManiaLivePlugins\\eXpansion\\ManiaExchange\\ManiaExchange", "mxSearch", $login, "", "");
                break;
            case "admcontrol":
                $this->callPublicMethod("ManiaLivePlugins\\eXpansion\\Adm\\Adm", "serverControlMain", $login);
                break;
            case "help":
                $this->callPublicMethod("ManiaLivePlugins\\eXpansion\\Faq\\Faq", "showFaq", $login, "toc", null);
                break;
            case "hudMove":
                $this->callPublicMethod("ManiaLivePlugins\\eXpansion\\Gui\\Gui", "hudCommands", $login, "move");
                break;
            case "hudLock":
                $this->callPublicMethod("ManiaLivePlugins\\eXpansion\\Gui\\Gui", "hudCommands", $login, "lock");
                break;
            case "hudConfig":
                $this->callPublicMethod("ManiaLivePlugins\\eXpansion\\Gui\\Gui", "showConfigWindow", $login, $entries);
                break;
            case "hudReset":
                $this->callPublicMethod("ManiaLivePlugins\\eXpansion\\Gui\\Gui", "hudCommands", $login, "reset");
                break;
            case "stats":
                $this->callPublicMethod("ManiaLivePlugins\\eXpansion\\Statistics\\Statistics", "showTopWinners", $login);
                break;
            case "serverinfo":
                $this->callPublicMethod("ManiaLivePlugins\\eXpansion\\Core\\Core", "showInfo", $login);
                break;
	    case "admreplay":
                $adminGrp->adminCmd($login, "replay");
                break;
        }
    }

    function exp_admin_added($login) {
        $this->onPlayerConnect($login, false);
    }

    function exp_admin_removed($login) {
        $this->onPlayerConnect($login, false);
    }

    function addSeparator($title, $isAdmin, $pluginId = null) {
        /*$item = new Structures\Menuitem($title, null, null, $isAdmin, true);
        $hash = spl_object_hash($item);
        $this->menuItems[$hash] = $item; */
    }

    function addItem($title, $icon, array $callback, $isAdmin, $pluginid = null) {
        /*if (is_callable($callback)) {
            $item = new Structures\Menuitem($title, $icon, $callback, $isAdmin);
            $hash = spl_object_hash($item);
            $this->menuItems[$hash] = $item;            
        } else {
            $this->console("Adding a button failed from plugin:" . $pluginid . " button callback is not valid.");
        } */
    }

    function reDraw() {
	
    }

    function onPlayerConnect($login, $isSpectator) {                                
	
        $submenu = Gui\Widgets\Submenu::Create($login);
        $menu = $submenu->getMenu();

        $submenu->addItem($menu, __("Help...", $login), $this->actions['help']);

        $maps = $submenu->addSubMenu($menu, __("Map", $login));
        $submenu->addItem($maps, __("List Maps...", $login), $this->actions['maplist']);
        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, "map_add")) {
            $submenu->addItem($maps, __("Add local map...", $login), $this->actions['addMaps']);
            $submenu->addItem($maps, __("Mania-Exchange...", $login), $this->actions['admmx']);
        }

        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, "map_remove")) {
            $submenu->addItem($maps, __("Remove this", $login), $this->actions['admremovemap']);
            $submenu->addItem($maps, __("Trash this", $login), $this->actions['admtrashmap']);
        }

        $stats = $submenu->addSubMenu($menu, __("Stats", $login));
        $submenu->addItem($stats, __("Show Records...", $login), $this->actions['maprecords']);
        $submenu->addItem($stats, __("Statistics...", $login), $this->actions['stats']);
        $submenu->addItem($stats, __("Server info...", $login), $this->actions['serverinfo']);

        $player = $submenu->addSubMenu($menu, __("Players", $login));
        $submenu->addItem($player, __("List Players...", $login), $this->actions['playerlist']);
        $submenu->addItem($player, "", null);
        $submenu->addItem($player, __("Rage Quit...", $login), $this->actions['quit']);

        $hud = $submenu->addSubMenu($menu, __("Hud", $login));
        $submenu->addItem($hud, __("Move Positions", $login), $this->actions['hudMove']);
        $submenu->addItem($hud, __("Lock Positions", $login), $this->actions['hudLock']);
        $submenu->addItem($hud, __("Show/Hide elements...", $login), $this->actions['hudConfig']);
        $submenu->addItem($hud, __("Reset Positions", $login), $this->actions['hudReset']);

        $votes = $submenu->addSubMenu($menu, __("Votes", $login));
        $submenu->addItem($votes, __("Vote Restart", $login), $this->actions['voteres']);
        $submenu->addItem($votes, __("Vote Skip", $login), $this->actions['voteskip']);
        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, "admin_cancelvote"))
            $submenu->addItem($votes, __("Cancel Vote", $login), $this->actions['admcancel']);

        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, "server_admin")) {
            $adm = $submenu->addSubMenu($menu, __("Admin", $login));

            if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, "admin_restart"))
                $submenu->addItem($adm, __("Instant Restart", $login), $this->actions['admres']);
	    
	    if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, "admin_restart"))
                $submenu->addItem($adm, __("Replay", $login), $this->actions['admreplay']);

            if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, "admin_skip"))
                $submenu->addItem($adm, __("Skip", $login), $this->actions['admskip']);

            if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, "admin_endround"))
                $submenu->addItem($adm, __("End Round", $login), $this->actions['admer']);


            $submenu->addItem($menu, __("Server Controls", $login), $this->actions['admcontrol']);
        }
        $submenu->show();
    }
    
}

?>