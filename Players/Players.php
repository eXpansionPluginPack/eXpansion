<?php

namespace ManiaLivePlugins\eXpansion\Players;

use ManiaLive\Event\Dispatcher;

class Players extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    public $msg_broadcast;

    public function exp_onInit() {
        parent::exp_onInit();
        //Oliverde8 Menu
        $this->addDependency(new \ManiaLive\PluginHandler\Dependency("eXpansion\\Chat_Admin"));
        if ($this->isPluginLoaded('oliverde8\HudMenu')) {
            Dispatcher::register(\ManiaLivePlugins\oliverde8\HudMenu\onOliverde8HudMenuReady::getClass(), $this);
        }
        Gui\Windows\Playerlist::$mainPlugin = $this;
    }

    public function exp_onLoad() {
        $this->msg_broadcast = exp_getMessage('%s$1 $z$s$fff is $f00broadcasting$fff at $lwww.twitch.tv$l, say hello to all the viewers :)');
    }

    public function exp_onReady() {
        $this->enableDedicatedEvents();
        $this->registerChatCommand("players", "showPlayerList", 0, true); // xaseco
        $this->registerChatCommand("plist", "showPlayerList", 0, true); // fast
        $this->registerChatCommand("play", "setPlay", 0, true); // fast
        $this->registerChatCommand("spec", "setSpec", 0, true); // fast

        if ($this->isPluginLoaded('eXpansion\Menu')) {
            $this->callPublicMethod('eXpansion\Menu', 'addSeparator', __('Players'), false);
            $this->callPublicMethod('eXpansion\Menu', 'addItem', __('Show Players'), null, array($this, 'showPlayerList'), false);
        }

        foreach ($this->storage->players as $player) {
            if ($player->isBroadcasting)
                $this->announceBroadcasting($player->login);
        }
        foreach ($this->storage->spectators as $player) {
            if ($player->isBroadcasting)
                $this->announceBroadcasting($player->login);
        }
    }

    public function onOliverde8HudMenuReady($menu) {
        $parent = $menu->findButton(array("admin", "Players"));
        $button["plugin"] = $this;
        if (!$parent) {
            $button["style"] = "Icons128x128_1";
            $button["substyle"] = "Profile";
            $parent = $menu->addButton("admin", "Players", $button);
        }

        $button["style"] = "Icons128x128_1";
        $button["substyle"] = "Profile";
        $button["plugin"] = $this;
        $button["function"] = 'showPlayerList';
        $parent = $menu->addButton($parent, "Show Players", $button);

        $parent = $menu->findButton(array("menu", "Players"));
        if (!$parent) {
            $button["style"] = "Icons128x128_1";
            $button["substyle"] = "Profile";
            $parent = $menu->addButton("menu", "Players", $button);
        }

        $button["style"] = "Icons128x128_1";
        $button["substyle"] = "Profile";
        $button["plugin"] = $this;
        $button["function"] = 'showPlayerList';
        $parent = $menu->addButton($parent, "Show Players", $button);
    }

    public function onPlayerDisconnect($login, $reason = null) {
        \ManiaLivePlugins\eXpansion\Players\Gui\Windows\Playerlist::Erase($login);
        $this->updateOpenedWindows();
    }

    public function onPlayerConnect($login, $isSpectator) {
        $this->updateOpenedWindows();
        $player = $this->storage->getPlayerObject($login);
        if ($player->isBroadcasting)
            $this->announceBroadcasting($player->login);
    }

    public function updateOpenedWindows() {
        $windows = \ManiaLivePlugins\eXpansion\Players\Gui\Windows\Playerlist::GetAll();
        foreach ($windows as $window) {

            $login = $window->getRecipient();
            $this->showPlayerList($login);
        }
    }

    /* public function onPlayerChangeSide($player, $oldSide) {
      $player = $this->storage->getPlayerObject($login);
      if ($player->isBroadcasting)
      $this->announceBroadcasting($player->login);
      } */

    public function setPlay($login) {
        $this->connection->forceSpectator($login, 2);
        $this->connection->forceSpectator($login, 0);
    }

    public function setSpec($login) {
        $this->connection->forceSpectator($login, 3);
    }

    public function announceBroadcasting($login) {
        $player = $this->storage->getPlayerObject($login);
        $this->exp_chatSendServerMessage($this->msg_broadcast, null, array($player->nickName));
    }

    public function showPlayerList($login) {
        \ManiaLivePlugins\eXpansion\Players\Gui\Windows\Playerlist::Erase($login);
        $window = \ManiaLivePlugins\eXpansion\Players\Gui\ Windows\Playerlist::Create($login);
        $window->setTitle('Players');

        $window->setSize(160, 100);
        $window->centerOnScreen();
        $window->show();
    }

    public function onPlayerInfoChanged($playerInfo) {
        $this->updateOpenedWindows();
    }

}

?>
