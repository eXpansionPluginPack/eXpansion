<?php

namespace ManiaLivePlugins\eXpansion\Maps;

use ManiaLive\Event\Dispatcher;

class Maps extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    public function exp_onInit() {
       parent::exp_onInit();
         //Oliverde8 Menu
        if ($this->isPluginLoaded('oliverde8\HudMenu')) {
            Dispatcher::register(\ManiaLivePlugins\oliverde8\HudMenu\onOliverde8HudMenuReady::getClass(), $this);
        }
    }
    
    public function exp_onReady() {
        $this->enableDedicatedEvents();
        Gui\Windows\Maplist::$mapsPlugin = $this;

        if ($this->isPluginLoaded('eXpansion\Menu')) {
            $this->callPublicMethod('eXpansion\Menu', 'addSeparator', 'Maps', false);
            $this->callPublicMethod('eXpansion\Menu', 'addItem', 'List maps', null, array($this, 'showMapList'), false);
            $this->callPublicMethod('eXpansion\Menu', 'addItem', 'Add map', null, array($this, 'addMaps'), true);
        }


        if ($this->isPluginLoaded('Standard\Menubar'))
            $this->buildMenu();
    }
    
    public function onOliverde8HudMenuReady($menu) {
        
        $button["style"] = "UIConstructionSimple_Buttons";
        $button["substyle"] = "Drive";        
        $button["plugin"] = $this;
        $parent = $menu->findButton(array('menu','Maps'));
        if(!$parent){
            $parent = $menu->addButton('menu', "Maps", $button);
        }
        
        $button["style"] = "Icons128x128_1";
        $button["substyle"] = "Drive";  
		$button["plugin"] = $this;
        $button["function"] = 'showMapList';
		$menu->addButton($parent, "List all Maps", $button);
        
        $button["substyle"] = "newTrack"; 
        $button["function"] = 'addMaps';
		$parent = $menu->addButton($parent, "Add Map", $button);
     }

    public function onPlayerDisconnect($login) {
        Gui\Windows\Maplist::Erase($login);
        Gui\Windows\AddMaps::Erase($login);
    }

    public function buildMenu() {
        $this->callPublicMethod('Standard\Menubar', 'initMenu', \ManiaLib\Gui\Elements\Icons128x128_1::Challenge);
        $this->callPublicMethod('Standard\Menubar', 'addButton', 'List all maps on server', array($this, 'showMapList'), false);
        $this->callPublicMethod('Standard\Menubar', 'addButton', 'Add local map on server', array($this, 'addMaps'), true);

        // user call votes disabled since dedicated doesn't support them atm.
        //  $this->callPublicMethod('Standard\Menubar', 'addButton', 'Vote for skip map', array($this, 'voteSkip'), false);
        //  $this->callPublicMethod('Standard\Menubar', 'addButton', 'Vote for replay map', array($this, 'voteRestart'), false);
    }

    /**
     * 
     * @param string $login
     * @todo enable the method for menu, currently votes are not working!
     */
    public function voteRestart($login) {
        //    $this->connection->callVoteRestartMap();

        $vote = new \DedicatedApi\Structures\Vote();
        $vote->callerLogin = $login;
        $vote->cmdName = "Cmd name";
        $vote->cmdParam = array("param");
        $this->connection->callVote($vote, 0.5, 0, 0);
        $this->connection->chatSendServerMessage($login . " custom vote restart");
    }

    public function onVoteUpdated($stateName, $login, $cmdName, $cmdParam) {
        $message = $stateName . " -> " . $login . " -> " . $cmdName . " -> " . $cmdParam . "\n";
        $this->connection->chatSendServerMessage($message);
    }

    public function voteSkip($login) {
        $this->connection->callVoteNextMap();
        $this->connection->chatSendServerMessage($login . " vote skip");
    }

    public function showMapList($login) {
        Gui\Windows\Maplist::Erase($login);

        if ($this->isPluginLoaded('eXpansion\LocalRecords'))
            Gui\Windows\MapList::$records = $this->callPublicMethod('eXpansion\LocalRecords', 'getRecords');

        $window = Gui\Windows\Maplist::Create($login);
        $window->setTitle(__('Maps on server'));
        $window->centerOnScreen();
        $window->setSize(140, 100);
        $window->show();
    }

    public function removeMap($login, $mapNumber) {
        if (!\ManiaLive\Features\Admin\AdminGroup::contains($login)) {
            $this->connection->chatSendServerMessage(__("You are not allowed to do this!"), $login);
            return;
        }

        try {

            $player = $this->storage->players[$login];
            $map = $this->storage->maps[$mapNumber];
            
            $this->connection->chatSendServerMessage(__('Admin %s $z$s$fff removed map %s $z$s$fff from the playlist.',$login , $player->nickName, $map->name));
            $this->connection->removeMap($map->fileName);
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(__("Error: %s",$login,$e->getMessage()));
        }
    }

    public function onMapListModified($curMapIndex, $nextMapIndex, $isListModified) {
        if ($isListModified) {
            $windows = Gui\Windows\Maplist::GetAll();

            foreach ($windows as $window) {
                $login = $window->getRecipient();
                $this->showMapList($login);
            }
        }
    }

    public function addMaps($login) {
        Gui\Windows\AddMaps::Erase($login);
        $window = Gui\Windows\AddMaps::Create($login);
        $window->setTitle('Add Maps on server');
        $window->centerOnScreen();
        $window->setSize(120, 100);
        $window->show();
    }

}
?>

