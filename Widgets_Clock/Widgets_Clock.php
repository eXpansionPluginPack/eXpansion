<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Clock;

class Widgets_Clock extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    function exp_onLoad() {
        $this->enableDedicatedEvents();
    }

    function exp_onReady() {
        $this->displayWidget();
    }

    /**
     * displayWidget(string $login)
     * @param string $login
     */
    function displayWidget($login = null) {
        $info = Gui\Widgets\Clock::Create($login);
        $info->setSize(40, 60);
        $info->setPosition(-159, 89);
        $info->show();
    }

    public function onPlayerConnect($login, $isSpectator) {
        $this->displayWidget($login);
    }

    public function onBeginMatch() {
        $this->displayWidget();
    }

    public function onEndMatch($rankings, $winnerTeamOrMap) {
        Gui\Widgets\Clock::EraseAll();
    }

}
?>

