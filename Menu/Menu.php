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
        $this->actions['voteres'] = $actionHandler->createAction(array($this, "actions"), "voteres");
        $this->actions['voteskip'] = $actionHandler->createAction(array($this, "actions"), "voteskip");
        $this->actions['admres'] = $actionHandler->createAction(array($this, "actions"), "admres");
        $this->actions['admskip'] = $actionHandler->createAction(array($this, "actions"), "admskip");
        $this->actions['admer'] = $actionHandler->createAction(array($this, "actions"), "admer");
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
        $submenu->addItem("Maps List", $this->actions['maplist']);
        $submenu->addItem("Players  List", $this->actions['playerlist']);
        $submenu->addItem("", null);
        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, "admin_restart")) {
            $submenu->addItem("Adm Restart", $this->actions['admres']);
        } else {
            $submenu->addItem("Vote Restart", $this->actions['voteres']);
        }
        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, "admin_skip")) {
            $submenu->addItem("Adm Skip", $this->actions['admskip']);
        } else {
            $submenu->addItem("Vote Skip", $this->actions['voteskip']);
        }
        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, "admin_endround")) {
            $submenu->addItem("Adm End Round", $this->actions['admer']);
        }
        $submenu->addItem("", null);
        $submenu->addItem("Leave Server", $this->actions['quit']);
        $submenu->show();
    }

    public function onPlayerDisconnect($login, $reason = null) {
        MenuPanel::Erase($login);
    }

}

?>