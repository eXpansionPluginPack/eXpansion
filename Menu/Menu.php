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
        if ($this->isPluginLoaded("eXpansion\AdminGroups")) {
            Dispatcher::register(\ManiaLivePlugins\eXpansion\AdminGroups\Events\Event::getClass(), $this);
        }

        $actionHandler = \ManiaLive\Gui\ActionHandler::getInstance();

        $this->actions['playerlist'] = $actionHandler->createAction(array($this, "actions"), "playerlist");
        $this->actions['maplist'] = $actionHandler->createAction(array($this, "actions"), "maplist");
        $this->actions['maprecords'] = $actionHandler->createAction(array($this, "actions"), "maprecords");
        $this->actions['voteres'] = $actionHandler->createAction(array($this, "actions"), "voteres");
        $this->actions['voteskip'] = $actionHandler->createAction(array($this, "actions"), "voteskip");
        $this->actions['admres'] = $actionHandler->createAction(array($this, "actions"), "admres");
        $this->actions['admskip'] = $actionHandler->createAction(array($this, "actions"), "admskip");
        $this->actions['admer'] = $actionHandler->createAction(array($this, "actions"), "admer");
        $this->actions['admcancel'] = $actionHandler->createAction(array($this, "actions"), "admcancel");
        $this->actions['admremovemap'] = $actionHandler->createAction(array($this, "actions"), "admremovemap");
        $this->actions['admtrashmap'] = $actionHandler->createAction(array($this, "actions"), "admtrashmap");
        $this->actions['quit'] = $actionHandler->createAction(array($this, "actions"), "quit");
    }

    function actions($login, $action) {
        $adminGrp = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance();

        switch ($action) {
            case "playerlist":
                $this->callPublicMethod("eXpansion\Players", "showPlayerList", $login);
                break;
            case "maplist":
                $this->callPublicMethod("eXpansion\Maps", "showMapList", $login);
                break;
            case "maprecords":
                $this->callPublicMethod("eXpansion\LocalRecords", "showRecsWindow", $login, Null);
                break;
            case "voteres":
                $this->callPublicMethod("eXpansion\Votes", "vote_restart", $login);
                break;
            case "voteskip":
                $this->callPublicMethod("eXpansion\Votes", "vote_skip", $login);
                break;
            case "quit":
                $this->connection->kick($login, "Thanks for visiting, and welcome back soon :)");
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
        }
    }

    function exp_admin_added($login) {
        $this->onPlayerConnect($login, false);
    }

    function exp_admin_removed($login) {
        $this->onPlayerConnect($login, false);
    }

    function addSeparator($title, $isAdmin, $pluginId = null) {
        $item = new Structures\Menuitem($title, null, null, $isAdmin, true);
        $hash = spl_object_hash($item);
        $this->menuItems[$hash] = $item;
    }

    function addItem($title, $icon, array $callback, $isAdmin, $pluginid = null) {
        if (is_callable($callback)) {
            $item = new Structures\Menuitem($title, $icon, $callback, $isAdmin);
            $hash = spl_object_hash($item);
            $this->menuItems[$hash] = $item;
            $this->reDraw();
        } else {
            $this->console("Adding a button failed from plugin:" . $pluginid . " button callback is not valid.");
        }
    }

    function reDraw() {
        foreach ($this->storage->players as $player)
            $this->onPlayerConnect($player->login, false);
        foreach ($this->storage->spectators as $player)
            $this->onPlayerConnect($player->login, true);
    }

    function onPlayerConnect($login, $isSpectator) {
        MenuPanel::Erase($login);
        Gui\Widgets\Submenu::Erase($login);
        $info = MenuPanel::Create($login);
        $info->setSize(60, 90);
        $info->setPosition(150, 35);
        $info->setLayer(\ManiaLive\Gui\Window::LAYER_NORMAL);
        $info->setItems($this->menuItems);
        $info->setScale(0.8);
        $info->show();

        $submenu = Gui\Widgets\Submenu::Create($login);
        $menu = $submenu->getMenu();

        $maps = $submenu->addSubMenu($menu, __("Map", $login));

        $submenu->addItem($maps, __("List all maps", $login), $this->actions['maplist']);
        $submenu->addItem($maps, __("Show Records", $login), $this->actions['maprecords']);
        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, "map_remove")) {
            $submenu->addItem($maps, "", null);
            $submenu->addItem($maps, __("Remove this map", $login), $this->actions['admremovemap']);
            $submenu->addItem($maps, __("Trash this map", $login), $this->actions['admtrashmap']);
        }
        $submenu->addItem($menu, __("Players List", $login), $this->actions['playerlist']);
        $submenu->addItem($menu, "", null);

        $adm = $submenu->addSubMenu($menu, __("Admin", $login));

        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, "admin_restart"))
            $submenu->addItem($adm, __("Restart", $login), $this->actions['admres']);

        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, "admin_skip"))
            $submenu->addItem($adm, __("Skip", $login), $this->actions['admskip']);

        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, "admin_endround"))
            $submenu->addItem($adm, __("End Round", $login), $this->actions['admer']);

        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, "admin_cancelvote"))
            $submenu->addItem($adm, __("Cancel Vote", $login), $this->actions['admcancel']);

        $votes = $submenu->addSubMenu($menu, __("Votes", $login));
        $submenu->addItem($votes, __("Vote Restart", $login), $this->actions['voteres']);
        $submenu->addItem($votes, __("Vote Skip", $login), $this->actions['voteskip']);



        $submenu->addItem($menu, "", null);
        $submenu->addItem($menu, __("Leave Server", $login), $this->actions['quit']);
        $submenu->show();
    }

    public function onPlayerDisconnect($login, $reason = null) {
        MenuPanel::Erase($login);
    }

}

?>