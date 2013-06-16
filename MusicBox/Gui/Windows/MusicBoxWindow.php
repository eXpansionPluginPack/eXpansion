<?php

namespace ManiaLivePlugins\eXpansion\MusicBox\Gui\Windows;

class MusicBoxWindow extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {
    
    private $navigator;
    

    function onConstruct() {
        $this->setTitle('MusicBox - select a music for next track');
        
        $this->navigator = new \ManiaLive\Gui\Controls\PageNavigator();
        $this->addComponent($this->navigator);
    }
    
    
    function destroy() {
        $this->navigator->destroy();
                
        parent::destroy();
    }

}