<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Clock;

class Widgets_Clock extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    function exp_onLoad() {
        $this->enableDedicatedEvents();
    }

    function exp_onReady() {
        $this->updateWidget();
    }

    /**
     * displayWidget(string $login)
     * @param string $login
     */
    function displayWidget($login) {
        $info = Gui\Widgets\Clock::Create($login);
        $info->setSize(58, 11);
        $info->setPosition(-161, 90.5);
        $info->setScale(0.8);
        $info->setPlayersCount(count($this->storage->players), count($this->storage->spectators));
        $info->setServerName($this->storage->server->name);
        $info->show();
    }

    public function onPlayerConnect($login, $isSpectator) {
        $this->displayWidget($login);
    }

    public function onPlayerDisconnect($login, $disconnectionReason = null) {
        Gui\Widgets\Clock::Erase($login);
    }

    public function onBeginMatch() {
        //$this->updateWidget();
    }

    public function onPlayerInfoChanged($playerInfo) {
        //$this->updateWidget();
    }

    public function updateWidget() {
        foreach ($this->storage->players as $login => $player)
            $this->displayWidget($login);
        foreach ($this->storage->spectators as $login => $player)
            $this->displayWidget($login);
    }

}
?>

