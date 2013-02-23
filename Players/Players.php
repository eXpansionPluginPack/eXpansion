<?php

namespace ManiaLivePlugins\eXpansion\Players;

class Players extends  \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    public function exp_onReady() {
        $this->enableDedicatedEvents();

        if ($this->isPluginLoaded('Standard\Menubar'))
            $this->buildMenu();

        if ($this->isPluginLoaded('eXpansion\Menu')) {
            $this->callPublicMethod('eXpansion\Menu', 'addSeparator', 'Players', false);
            $this->callPublicMethod('eXpansion\Menu', 'addItem', 'Show Players', null, array($this, 'showPlayerList'), false);
        }
    }

    public function onPlayerDisconnect($login) {
        \ManiaLivePlugins\eXpansion\Players\Gui\Windows\Playerlist::Erase($login);
        $this->updateOpenedWindows();
    }
    
    public function onPlayerConnect($login, $isSpectator) {
        $this->updateOpenedWindows();        
    }
    
    public function updateOpenedWindows() {
        $windows = \ManiaLivePlugins\eXpansion\Players\Gui\Windows\Playerlist::GetAll();
        foreach ($windows as $window) {
            $login = $window->getRecipient();
            $this->showPlayerList($login);
        }
    }

    public function buildMenu() {
        $this->callPublicMethod('Standard\Menubar', 'initMenu', \ManiaLib\Gui\Elements\Icons64x64_1::Buddy);
        $this->callPublicMethod('Standard\Menubar', 'addButton', 'Players', array($this, 'showPlayerList'), false);
    }

    public function showPlayerList($login) {
        \ManiaLivePlugins\eXpansion\Players\Gui\Windows\Playerlist::Erase($login);
        $window = \ManiaLivePlugins\eXpansion\Players\Gui\Windows\Playerlist::Create($login);
        $window->setTitle('Players');
        $window->setSize(120, 100);
        $window->centerOnScreen();
        $window->show();
    }
    
    public function onPlayerInfoChanged($playerInfo) {
         $this->updateOpenedWindows();                 
   }
    
    

}

?>
