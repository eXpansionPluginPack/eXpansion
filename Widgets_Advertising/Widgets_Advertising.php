<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Advertising;

/**
 * Description of Widgets_Advertising
 *
 * @author Petri
 */
class Widgets_Advertising extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $config;
    
    
    public function exp_onReady() {
        $this->config = Config::GetInstance();
        foreach ($this->storage->players as $login => $player)
            $this->displayWidget($login);
        foreach ($this->storage->spectators as $login => $player)
            $this->displayWidget($login);
    }

    public function onPlayerConnect($login, $isSpectator) {
        $this->displayWidget($login);
    }

    public function onPlayerDisconnect($login, $disconnectionReason) {
        
    }

    public function displayWidget($login) {
        $widget = Gui\Widgets\WidgetAd::Create($login);
        $widget->setPosition(-60);
    }

}
