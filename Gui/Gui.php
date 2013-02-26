<?php

namespace ManiaLivePlugins\eXpansion\Gui;

use ManiaLive\Utilities\Console;


class Gui extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    public function exp_onInit() {
        $this->setVersion("0.1");
    }
    
    public function exp_onReady() {
        $this->enableDedicatedEvents();        
    }
    
    public function onPlayerManialinkPageAnswer($playerUid, $login, $answer, array $entries) {
        echo $answer;
        print_r($entries);
        
    }
    
    
}

?>