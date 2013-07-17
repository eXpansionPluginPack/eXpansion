<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Clock;

class Widgets_Clock extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    function exp_onLoad() {
        $this->enableDedicatedEvents();
    }

    function exp_onReady() {
        foreach ($this->storage->players as $login => $player)
            $this->displayWidget($login);
        foreach ($this->storage->spectators as $login => $player)
            $this->displayWidget($login);
    }

    /**
     * displayWidget(string $login)
     * @param string $login
     */
    function displayWidget($login) {       
        $info = Gui\Widgets\Clock::Create($login);
        $info->setSize(60, 12);
        $info->show();
    }

    public function onPlayerConnect($login, $isSpectator) {        
        $this->displayWidget($login);
    }

    public function onPlayerDisconnect($login, $disconnectionReason = null) {
        Gui\Widgets\Clock::Erase($login);
    }

    public function onBeginMatch() {
        
    }

    public function onEndMatch($rankings, $winnerTeamOrMap) {
        
    }

}
?>

