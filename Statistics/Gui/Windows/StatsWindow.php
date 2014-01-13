<?php

namespace ManiaLivePlugins\eXpansion\Statistics\Gui\Windows;



abstract class StatsWindow extends \ManiaLivePlugins\eXpansion\Gui\Windows\PagerWindow {

    public static $menuFrame = null;
    
    protected function onConstruct() {
        parent::onConstruct();
        
        $this->setPagerPosition(42, 0);
        
        $this->mainFrame->addComponent(self::$menuFrame);
    }
  
}

?>
