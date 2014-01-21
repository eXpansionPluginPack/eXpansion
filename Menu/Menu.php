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

        $this->actions['playerlist'] = $actionHandler->createAction(array($this, "showPlayers"));
        $this->actions['maplist'] = $actionHandler->createAction(array($this, "showMapList"));
        $this->actions['voteres'] = $actionHandler->createAction(array($this, "vote_res"));
        $this->actions['voteskip'] = $actionHandler->createAction(array($this, "vote_skip"));
        $this->actions['quit'] = $actionHandler->createAction(array($this, "quit"));
    }

    function showMapList($login) {
        $this->callPublicMethod("eXpansion\Maps", "showMapList", $login);
    }

    function quit($login) {
        $this->connection->kick($login, "Thanks for visiting, and welcome back soon :)");
    }

    function showPlayers($login) {
        $this->callPublicMethod("eXpansion\Players", "showPlayerList", $login);
    }

    function vote_res($login) {
        $this->callPublicMethod("eXpansion\Votes", "vote_restart",  $login);
    }

    function vote_skip($login) {
        $this->callPublicMethod("eXpansion\Votes", "vote_skip", $login);
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
        $submenu->addItem("Vote Restart", $this->actions['voteres']);
        $submenu->addItem("Vote Skip", $this->actions['voteskip']);
        $submenu->addItem("", null);
        $submenu->addItem("Leave Server", $this->actions['quit']);
        $submenu->show();
    }

    public function onPlayerDisconnect($login, $reason = null) {
        MenuPanel::Erase($login);
    }

}

?>