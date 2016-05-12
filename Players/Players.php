<?php

namespace ManiaLivePlugins\eXpansion\Players;

use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\MatchMakingLobby\Windows\PlayerList;

class Players extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{
    public $msg_broadcast;

    public function exp_onInit()
    {
        parent::exp_onInit();

        $this->addDependency(new \ManiaLive\PluginHandler\Dependency("\\ManiaLivePlugins\\eXpansion\\Chat_Admin\\Chat_Admin"));

        Gui\Windows\Playerlist::$mainPlugin = $this;
    }

    public function eXpOnLoad()
    {
        $this->msg_broadcast = exp_getMessage('%s$1 $z$s$fff is $f00broadcasting$fff at $lwww.twitch.tv$l, say hello to all the viewers :)');
    }

    public function eXpOnReady()
    {
        $this->enableDedicatedEvents();
        $this->registerChatCommand("players", "showPlayerList", 0, true); // xaseco
        $this->registerChatCommand("plist", "showPlayerList", 0, true); // fast
        $this->registerChatCommand("play", "setPlay", 0, true); // fast
        $this->registerChatCommand("spec", "setSpec", 0, true); // fast

        $this->setPublicMethod("showPlayerList");

        if ($this->isPluginLoaded('eXpansion\Menu')) {
            $this->callPublicMethod('\ManiaLivePlugins\eXpansion\Menu', 'addSeparator', __('Players'), false);
            $this->callPublicMethod('\ManiaLivePlugins\eXpansion\Menu', 'addItem', __('Show Players'), null, array($this, 'showPlayerList'),
                false);
        }
    }

    public function onPlayerDisconnect($login, $reason = null)
    {
        \ManiaLivePlugins\eXpansion\Players\Gui\Windows\Playerlist::Erase($login);
        // needs to be removed, autoupdate windows doesn't work good with high number of players
        //$this->updateOpenedWindows();
    }

    public function onPlayerConnect($login, $isSpectator)
    {
        // needs to be removed, autoupdate windows doesn't work good with high number of players
        //$this->updateOpenedWindows();
        $player = $this->storage->getPlayerObject($login);
        if ($player->isBroadcasting) $this->announceBroadcasting($player->login);
    }

    public function updateOpenedWindows()
    {
        $windows = \ManiaLivePlugins\eXpansion\Players\Gui\Windows\Playerlist::GetAll();
        foreach ($windows as $window) {
            $window->redraw($window->getRecipient());
        }
    }

    public function setPlay($login)
    {
        $player = $this->storage->getPlayerObject($login);
        if (!$player->forceSpectator) {
            $this->connection->forceSpectator($login, 2);
            $this->connection->forceSpectator($login, 0);
        }
    }

    public function setSpec($login)
    {
        $this->connection->forceSpectator($login, 3);
    }

    public function announceBroadcasting($login)
    {
        $player = $this->storage->getPlayerObject($login);
        $this->eXpChatSendServerMessage($this->msg_broadcast, null, array($player->nickName));
    }

    public function showPlayerList($login)
    {
        \ManiaLivePlugins\eXpansion\Players\Gui\Windows\Playerlist::Erase($login);
        $window = \ManiaLivePlugins\eXpansion\Players\Gui\Windows\Playerlist::Create($login);
        $window->setTitle('Players');

        $window->setSize(140, 100);
        $window->centerOnScreen();
        $window->show();
    }

    public function onPlayerInfoChanged($playerInfo)
    {
        // $this->updateOpenedWindows();
    }

    function eXpOnUnload()
    {
        PlayerList::EraseAll();
    }
}

?>
