<?php

namespace ManiaLivePlugins\eXpansion\Menu;

use \ManiaLivePlugins\eXpansion\Menu\Gui\Widgets\MenuPanel;
use \ManiaLivePlugins\eXpansion\Menu\Structures\Menuitem;

class Menu extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $menuItems = array();

    function exp_onInit() {
        $this->setPublicMethod("addItem");
        $this->setPublicMethod("addSeparator");        
    }

    function exp_onReady() {
        $this->enableDedicatedEvents();
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
            \ManiaLive\Utilities\Console::println("Adding a button failed from plugin:" . $pluginid . " button callback is not valid.");
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
        $info = MenuPanel::Create($login);
        $info->setSize(60, 20);
        $info->setPosition(150, 50);
        $info->setItems($this->menuItems);
        $info->show();
    }

    public function onPlayerDisconnect($login) {
        MenuPanel::Erase($login);       
    }

}

?>