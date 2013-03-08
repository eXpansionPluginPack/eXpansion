<?php

namespace ManiaLivePlugins\eXpansion\Maps;

use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\Maps\Structures\MapWish;
use ManiaLivePlugins\eXpansion\Maps\Gui\Widgets\NextMapWidget;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
class Maps extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    /** @var array(MapWish) */
    private $nextMaps = array();

    /* will be used when custom votes works again */

    /** @var MapWish */
    private $voteItem;

    public function exp_onInit() {
        parent::exp_onInit();

//Oliverde8 Menu
        if ($this->isPluginLoaded('oliverde8\HudMenu')) {
            Dispatcher::register(\ManiaLivePlugins\oliverde8\HudMenu\onOliverde8HudMenuReady::getClass(), $this);
        }
    }

    public function exp_onReady() {
        $this->enableDedicatedEvents();

        $cmd = AdminGroups::addAdminCommand('map remove', $this, 'chat_removeMap', 'server_maps');
        $cmd->setHelp(exp_getMessage('Removes current map from the playlist.'));
        $cmd->setMinParam(1);        
        AdminGroups::addAlias($cmd, "remove");

        $this->registerChatCommand('list', "showMapList", 0, true);
        $this->registerChatCommand('maps', "showMapList", 0, true);

        if ($this->isPluginLoaded('eXpansion\Menu')) {
            $this->callPublicMethod('eXpansion\Menu', 'addSeparator', __('Maps'), false);
            $this->callPublicMethod('eXpansion\Menu', 'addItem', __('List maps'), null, array($this, 'showMapList'), false);
            $this->callPublicMethod('eXpansion\Menu', 'addItem', __('Add map'), null, array($this, 'addMaps'), true);
        }


        if ($this->isPluginLoaded('Standard\Menubar'))
            $this->buildMenu();

        Gui\Windows\Maplist::Initialize($this);

        $widget = NextMapWidget::Create(null);
        $widget->setPosition(136, 74);
        $widget->setMap($this->storage->nextMap);
        $widget->show();
    }

    public function onOliverde8HudMenuReady($menu) {

        $button["style"] = "UIConstructionSimple_Buttons";
        $button["substyle"] = "Drive";
        $button["plugin"] = $this;
        $parent = $menu->findButton(array('menu', 'Maps'));
        if (!$parent) {
            $parent = $menu->addButton('menu', "Maps", $button);
        }

        $button["style"] = "Icons128x128_1";
        $button["substyle"] = "Drive";
        $button["plugin"] = $this;
        $button["function"] = 'showMapList';
        $menu->addButton($parent, "List all Maps", $button);

        $button["substyle"] = "newTrack";
        $button["function"] = 'addMaps';
        $menu->addButton($parent, "Add Map", $button);
    }

    function onPlayerConnect($login, $isSpectator) {
        $info = \ManiaLivePlugins\eXpansion\Maps\Gui\Widgets\NextMapWidget::Create($login);
        $info->setPosition(136, 74);
        $info->setMap($this->storage->nextMap);
        $info->show();
    }

    public function onPlayerDisconnect($login) {
        Gui\Windows\Maplist::Erase($login);
        Gui\Windows\AddMaps::Erase($login);
        NextMapWidget::Erase($login);
    }

    function onBeginMap($map, $warmUp, $matchContinuation) {
        NextMapWidget::EraseAll();
        $widget = NextMapWidget::Create(null);
        $widget->setPosition(136, 74);
        $widget->setMap($this->storage->nextMap);
        $widget->show();
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
        $vote = new \DedicatedApi\Structures\Vote();
        $vote->callerLogin = $login;
        $vote->cmdName = "Cmd name";
        $vote->cmdParam = array("param");
        $this->connection->callVote($vote, 0.3, 0, 0);
        $this->connection->chatSendServerMessage($login . " custom vote restart");
    }

    public function onVoteUpdated($stateName, $login, $cmdName, $cmdParam) {
        $message = $stateName . " -> " . $login . " -> " . $cmdName . " -> " . $cmdParam . "\n";
//  $this->connection->chatSendServerMessage($message);
    }

    public function voteSkip($login) {
        $this->connection->callVoteNextMap();
        $this->connection->chatSendServerMessage($login . " vote skip");
    }

    public function testme($login) {
        print "total number: " . count(Gui\Windows\Maplist::GetAll());
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

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {
        if ($restartMap) {
            return;
        }
        try {
            /** @var MapWish */
            $nextItem = array_shift($this->nextMaps);
            if ($nextItem == null)
                return;

            $nextMap = $nextItem->map;
            $player = $nextItem->player;

            $this->connection->chooseNextMap($nextMap->fileName);
            $this->connection->chatSendServerMessage(__('Next map will be %s $z$s$fff by %s, wished by %s', null, $nextMap->name, $nextMap->author, $player->nickName));
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(__('Error: %s', null, $e->getMessage()));
        }
    }

    public function chooseNextMap($login, \DedicatedApi\Structures\Map $map) {
        try {
            $player = $this->storage->getPlayerObject($login);
            if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::isInList($login)) {
//  if (sizeof($this->nextMaps) == 0) 
                $this->nextMaps[] = new MapWish($player, $map);
                $this->connection->chatSendServerMessage(__('Map %s $z$s$fff by %s, wished by %s $z$s$fff is added to next maps list.', $login, $map->name, $map->author, $player->nickName));
            } else {
                foreach ($this->nextMaps as $nextItems) {
                    if ($nextItems->player->login == $login) {
                        $this->connection->chatSendServerMessage(__('You have already map in next map wishes.', $login), $login);
                        return;
                    }
                }

                $this->nextMaps[] = new MapWish($player, $map);
                $this->connection->chatSendServerMessage(__('Map %s $z$s$fff by %s, wished by %s $z$s$fff is added to next maps list.', $login, $map->name, $map->author, $player->nickName));
            }
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(__('Error: %s', $login, $e->getMessage()));
        }
    }

    public function gotoMap($login, \DedicatedApi\Structures\Map $map) {
        try {
// $this->connection->jumpToMapIndex($mapNumber);
            $this->connection->chooseNextMap($map->fileName);
            $this->connection->nextMap();
            $map = $this->connection->getNextMapInfo();
            $this->connection->chatSendServerMessage(__('Speedjump to map %s $z$s$fff by %s', $login, $map->name, $map->author));
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(__('Error: %s', $login, $e->getMessage()));
        }
    }

    public function removeMap($login, \DedicatedApi\Structures\Map $map) {
        if (!\ManiaLive\Features\Admin\AdminGroup::contains($login)) {
            $this->connection->chatSendServerMessage(__("You are not allowed to do this!", $login), $login);
            return;
        }

        try {

            $player = $this->storage->getPlayerObject($login);

            $this->connection->chatSendServerMessage(__('Admin %s $z$s$fff removed map %s $z$s$fff from the playlist.', $login, $player->nickName, $map->name));
            $this->connection->removeMap($map->fileName);
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(__("Error: %s", $login, $e->getMessage()));
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

    function chat_removeMap($login, $params) {
        if (is_numeric($params[0])) 
        {
            if (is_object($this->storage->maps[$params[0]])){
             $this->removeMap($login, $this->storage->maps[$params[0]]);   
            }
            return;            
        }
        
        if ($params[0] == "this") {
            $this->removeMap($login, $this->storage->currentMap);     
            return;
        }
    }
    public function addMaps($login) {
        $window = Gui\Windows\AddMaps::Create($login);
        $window->setTitle('Add Maps on server');
        $window->centerOnScreen();
        $window->setSize(120, 100);
        $window->show();
    }

}
?>

