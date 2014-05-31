<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Clock;

use ManiaLivePlugins\eXpansion\Widgets_Clock\Gui\Widgets\Clock;

class Widgets_Clock extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    function exp_onLoad() {
        // $this->enableDedicatedEvents();
    }

    function exp_onReady() {
        $this->displayWidget(null);
    }

    /**
     * displayWidget(string $login)
     * @param string $login
     */
    function displayWidget($login) {
        $info = Gui\Widgets\Clock::Create(null);
        $info->setSize(60, 15);
        $info->setPosition(115, 89);
	$info->setScale(0.75);                
        $info->show();
    }

    function exp_onUnload()
    {
	Clock::EraseAll();
    }

}
?>

