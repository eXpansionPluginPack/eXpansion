<?php

namespace ManiaLivePlugins\eXpansion\Maps;

use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\Maps\Structures\MapWish;
use ManiaLivePlugins\eXpansion\Maps\Gui\Widgets\NextMapWidget;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;

class Maps extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    /** @var array(MapWish) */
    private $nextMaps = array();
    private $nextMapCount = 0;

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
        $button["substyle"] = "Browse";
        $button["plugin"] = $this;
        $button["function"] = 'showMapList';
        $menu->addButton($parent, "List all Maps", $button);

        //Don't think this is a good idea..  may be useful in the future for temp adds of local maps, though
        //$button["substyle"] = "NewTrack";
        //$button["function"] = 'addMaps';
        //$menu->addButton($parent, "Add Map", $button);

        $this->hudMenuAdminButtons($menu);
    }

    private function hudMenuAdminButtons($menu){

        $button["style"] = "UIConstructionSimple_Buttons";
        $button["substyle"] = "Drive";
        $button["plugin"] = $this;
        $parent = $menu->findButton(array('admin', 'Maps'));
        if (!$parent) {
            $parent = $menu->addButton('admin', "Maps", $button);
        }

        $button["style"] = "Icons64x64_1";
        $button["substyle"] = "Close";

        $button["plugin"] = $this;
        $button["function"] = "chat_removeMap";
        $button["params"] = "this";
        $button["permission"] = "server_maps";
        $menu->addButton($parent, "Remove Current Map", $button);

        $button["style"] = "Icons64x64_1";
        $button["substyle"] = "Sub";

        $button["plugin"] = $this;
        $button["function"] = "emptyWishes";
        $button["params"] = "this";
        $button["permission"] = "server_mapWishes";
        $menu->addButton($parent, "Empty Wish List", $button);

        $button["style"] = "Icons128x128_1";
        $button["substyle"] = "NewTrack";
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
        try {
            /** @var MapWish */
            $nextItem = array_shift($this->nextMaps);
            if ($nextItem == null)
                return;
            
            $this->nextMapCount = sizeof($this->nextMaps);            
            $nextMap = $nextItem->map;
            $player = $nextItem->player;
            $this->connection->chooseNextMap($nextMap->fileName);
            $this->exp_chatSendServerMessage(__('Next map will be %s $z$s$fff by %s, wished by %s', null, \ManiaLib\Utils\Formatting::stripCodes($nextMap->name, 'wosnm'), $nextMap->author, \ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm')));
        } catch (\Exception $e) {
            $this->exp_chatSendServerMessage(__('Error: %s', null, $e->getMessage()));
        }


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
        $this->exp_chatSendServerMessage($login . " custom vote restart");
    }

    public function onVoteUpdated($stateName, $login, $cmdName, $cmdParam) {
        $message = $stateName . " -> " . $login . " -> " . $cmdName . " -> " . $cmdParam . "\n";
//  $this->exp_chatSendServerMessage($message);
    }

    public function voteSkip($login) {
        $this->connection->callVoteNextMap();
        $this->exp_chatSendServerMessage($login . " vote skip");
    }

    public function testme($login) {
        print "total number: " . count(Gui\Windows\Maplist::GetAll());
    }

    public function showMapList($login) {
        Gui\Windows\Maplist::Erase($login);

        if ($this->isPluginLoaded('eXpansion\LocalRecords'))
            Gui\Windows\MapList::$records = $this->callPublicMethod('eXpansion\LocalRecords', 'getRecords');

        $window = Gui\Windows\Maplist::Create($login);
        $window->setTitle(__('Maps on server', $login));
        $window->centerOnScreen();
        $window->setSize(160, 100);
        $window->show();
    }

    public function chooseNextMap($login, \DedicatedApi\Structures\Map $map) {
        try {
            $player = $this->storage->getPlayerObject($login);
			if ($this->storage->currentMap->uId == $map->uId) {
				$this->exp_chatSendServerMessage(__('This map is currently playing...', $login), $login);
				return;
			}
            foreach ($this->nextMaps as $nextItems) {
                if ($nextItems->map->uId == $map->uId) {
                    $this->exp_chatSendServerMessage(__('This map is already in the next map wishes.', $login), $login);
                    return;
                }
            }
            if (!\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::isInList($login)) {
                foreach ($this->nextMaps as $nextItems) {
                    if ($nextItems->player->login == $login) {
                        $this->exp_chatSendServerMessage(__('You have already map in next map wishes.', $login), $login);
                        return;
                    }
                }
            }
            if ($this->nextMapCount == 0) {
                $this->connection->chooseNextMap($map->fileName);
            } else {
                $this->nextMaps[] = new MapWish($player, $map);
            }
            $this->nextMapCount++;
            $this->exp_chatSendServerMessage(__('Map %s $z$s$fff by %s, wished by %s $z$s$fff is added to next maps list.', null, \ManiaLib\Utils\Formatting::stripCodes($map->name, 'wosnm'), $map->author, \ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm')));
        } catch (\Exception $e) {
            $this->exp_chatSendServerMessage(__('Error: %s', $login, $e->getMessage()));
        }
    }

    public function gotoMap($login, \DedicatedApi\Structures\Map $map) {
        try {
            $this->connection->chooseNextMap($map->fileName);
            $this->connection->nextMap();
            $map = $this->connection->getNextMapInfo();
            $this->exp_chatSendServerMessage(__('Speedjump to map %s $z$s$fff by %s', null, \ManiaLib\Utils\Formatting::stripCodes($map->name, 'wosnm'), \ManiaLib\Utils\Formatting::stripCodes($map->author, 'wosnm')));
        } catch (\Exception $e) {
            $this->exp_chatSendServerMessage(__('Error: %s', $login, $e->getMessage()));
        }
    }

    public function removeMap($login, \DedicatedApi\Structures\Map $map) {
        if (!\ManiaLive\Features\Admin\AdminGroup::contains($login)) {
            $this->exp_chatSendServerMessage(__("You are not allowed to do this!", $login), $login);
            return;
        }

        try {

            $player = $this->storage->getPlayerObject($login);

            $this->exp_chatSendServerMessage(__('Admin %s $z$s$fff removed map %s $z$s$fff from the playlist.', $login, \ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm'), \ManiaLib\Utils\Formatting::stripCodes($map->name, 'wosnm')));
            $this->connection->removeMap($map->fileName);
        } catch (\Exception $e) {
            $this->exp_chatSendServerMessage(__("Error: %s", $login, $e->getMessage()));
        }
    }

    public function onMapListModified($curMapIndex, $nextMapIndex, $isListModified) {


        foreach (NextMapWidget::getAll() as $widget) {
            $widget->setMap($this->storage->nextMap);
            $widget->redraw($widget->getRecipient());
        }

        if ($isListModified) {
            $windows = Gui\Windows\Maplist::GetAll();

            foreach ($windows as $window) {
                $login = $window->getRecipient();
                $this->showMapList($login);
            }
        }
    }

    function chat_removeMap($login, $params) {
        if (is_numeric($params[0])) {
            if (is_object($this->storage->maps[$params[0]])) {
                $this->removeMap($login, $this->storage->maps[$params[0]]);
            }
            return;
        }

        if ($params[0] == "this") {
            $this->removeMap($login, $this->storage->currentMap);
            return;
        }
    }

    function emptyWishes($login){
		$player = $this->storage->getPlayerObject($login);
        $this->nextMaps = array();
        $this->nextMapCount = 0;
        $this->exp_chatSendServerMessage('Admin %s $z$s$fff emptied wish list.', null, array(\ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm')));
    }

    public function addMaps($login) {
        $window = Gui\Windows\AddMaps::Create($login);
        $window->setTitle('Add Maps on server');
        $window->centerOnScreen();
        $window->setSize(160, 100);
        $window->show();
    }

}
?>

