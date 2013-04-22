<?php

namespace ManiaLivePlugins\eXpansion\Maps;

use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\Maps\Config;
use ManiaLivePlugins\eXpansion\Maps\Structures\MapWish;
use ManiaLivePlugins\eXpansion\Maps\Gui\Widgets\NextMapWidget;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;

class Maps extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $config;

    private $queue = array();
    private $history = array();
    private $nextMap;
    private $tries = 0;

    /* will be used when custom votes works again */

    /** @var MapWish */
    private $voteItem;

    public function exp_onInit() {
        parent::exp_onInit();

        //Oliverde8 Menu
        if ($this->isPluginLoaded('oliverde8\HudMenu')) {
            Dispatcher::register(\ManiaLivePlugins\oliverde8\HudMenu\onOliverde8HudMenuReady::getClass(), $this);
        }

        $this->config = Config::getInstance();
        $this->config->bufferSize = $this->config->bufferSize + 1;

        $this->config->msg_addQueue = exp_getMessage($this->config->msg_addQueue);
        $this->config->msg_nextQueue = exp_getMessage($this->config->msg_nextQueue);
        $this->config->msg_nextMap = exp_getMessage($this->config->msg_nextMap);
        $this->config->msg_queueNow = exp_getMessage($this->config->msg_queueNow);
    }

    public function exp_onReady() {
        $this->enableDedicatedEvents();

        $cmd = AdminGroups::addAdminCommand('map remove', $this, 'chat_removeMap', 'server_maps');
        $cmd->setHelp(exp_getMessage('Removes current map from the playlist.'));
        $cmd->setMinParam(1);
        AdminGroups::addAlias($cmd, "remove");

        $this->registerChatCommand('list', "showMapList", 0, true);
        $this->registerChatCommand('maps', "showMapList", 0, true);
        $this->registerChatCommand('nextmap', "chat_nextMap", 0, true);
        $this->registerChatCommand('drop', "chat_dropQueue", 0, true);
        //$this->registerChatCommand('history', "chat_history", 0, true);
        //$this->registerChatCommand('queue', "chat_showQueue", 0, true);

        if ($this->isPluginLoaded('eXpansion\Menu')) {
            $this->callPublicMethod('eXpansion\Menu', 'addSeparator', __('Maps'), false);
            $this->callPublicMethod('eXpansion\Menu', 'addItem', __('List maps'), null, array($this, 'showMapList'), false);
            $this->callPublicMethod('eXpansion\Menu', 'addItem', __('Add map'), null, array($this, 'addMaps'), true);
        }

        if ($this->isPluginLoaded('Standard\Menubar'))
            $this->buildMenu();

        $this->nextMap = $this->storage->nextMap;

        Gui\Windows\Maplist::Initialize($this);

        if ($this->config->showNextMapWidget) {
            $widget = NextMapWidget::Create(null);
            $widget->setPosition(136, 74);
            $widget->setMap($this->nextMap);
            $widget->show();
        }

        $this->preloadHistory();
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
        if ($this->config->showNextMapWidget) {
            $info = \ManiaLivePlugins\eXpansion\Maps\Gui\Widgets\NextMapWidget::Create($login);
            $info->setPosition(136, 74);
            $info->setMap($this->nextMap);
            $info->show();
        }
    }

    public function onPlayerDisconnect($login, $reason = null) {
        Gui\Windows\Maplist::Erase($login);
        Gui\Windows\AddMaps::Erase($login);
        if ($this->config->showNextMapWidget)
            NextMapWidget::Erase($login);
    }

    function onBeginMap($map, $warmUp, $matchContinuation) {

        if (count($this->queue) > 0) {
            $queue = reset($this->queue);
            if ($queue->map->uId == $this->storage->currentMap->uId) {
                if ($queue->isTemp) {
                    try {
                        $this->connection->removeMap($queue->map->fileName);
                    } catch (\Exception $e) {
                        $this->exp_chatSendServerMessage(__("Error: %s", $login, $e->getMessage()));
                    }
                }
                array_shift($this->queue);
            } else {
                if ($this->tries < 3) {
                    $this->tries++;
                } else {
                    $this->tries = 0;
                    array_shift($this->queue);
                }
            }
        }

        if (count($this->queue) > 0) {
            $queue = reset($this->queue);
            $this->nextMap = $queue->map;
        } else {
            $this->nextMap = $this->storage->nextMap;
        }

        array_unshift($this->history, $this->storage->currentMap);
        if (count($this->history) > 10) {
            array_pop($this->history);
        }

        if ($this->config->showNextMapWidget) {
            NextMapWidget::EraseAll();
            $widget = NextMapWidget::Create(null);
            $widget->setPosition(136, 74);
            $widget->setMap($this->nextMap);
            $widget->show();
        }
    }

    public function onEndMatch ($rankings, $winnerTeamOrMap) {

        if (count($this->queue) > 0) {
            $queue = reset($this->queue);
            try {
                $this->connection->chooseNextMap($queue->map->fileName);
                if ($this->config->showEndMatchNotices) {
                    $this->exp_chatSendServerMessage($this->config->msg_nextQueue, null, array(\ManiaLib\Utils\Formatting::stripCodes($queue->map->name, 'wosnm'), $queue->map->author, \ManiaLib\Utils\Formatting::stripCodes($queue->player->nickName, 'wosnm'), $queue->player->login));
                }
            } catch (\Exception $e) {
                $this->exp_chatSendServerMessage(__('Error: %s', $login, $e->getMessage()));
            }
        } else {
            if ($this->config->showEndMatchNotices) {
                $this->exp_chatSendServerMessage($this->config->msg_nextMap, null, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->nextMap->name, 'wosnm'), $this->storage->nextMap->author));
            }
        }
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

    public function queueMap ($login, \DedicatedApi\Structures\Map $map, $isTemp = false) {
        try {
            $player = $this->storage->getPlayerObject($login);

            if ($this->storage->currentMap->uId == $map->uId) {
                $msg = exp_getMessage('#admin_error# $iThis map is currently playing...');
                $this->exp_chatSendServerMessage($msg, $login);
                return;
            }

            foreach ($this->queue as $queue) {
                if ($queue->map->uId == $map->uId) {
                    $msg = exp_getMessage('#admin_error# $iThis map is already in the queue...');
                    $this->exp_chatSendServerMessage($msg, $login);
                    return;
                }

                if (!\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::isInList($login) && $queue->player->login == $login) {
                    $msg = exp_getMessage('#admin_error# $iYou already have a map in the queue...');
                    $this->exp_chatSendServerMessage($msg, $login);
                    return;
                }
            }

            if (!\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::isInList($login) && $this->config->bufferSize > 0) {
                $i = 0;
                foreach ($this->history as $played) {
                    $i++;
                    if ($i <= $this->config->bufferSize) {
                        if ($played->uId == $map->uId) {
                            $msg = exp_getMessage('#admin_error# $iMap has been played too recently...');
                            $this->exp_chatSendServerMessage($msg, $login);
                            return;
                        }
                    } else {
                        break;
                    }
                }
            }

            $this->queue[] = new MapWish($player, $map, $isTemp);

            $queueCount = count($this->queue);
            if ($queueCount == 1) {
                $this->nextMap = $map;
                if ($this->config->showNextMapWidget) {
                    NextMapWidget::EraseAll();
                    $widget = NextMapWidget::Create(null);
                    $widget->setPosition(136, 74);
                    $widget->setMap($this->nextMap);
                    $widget->show();
                }
            }
            if ($queueCount <= 31)
                $queueCount = date('jS',strtotime('2007-01-' . $queueCount));

            $this->exp_chatSendServerMessage($this->config->msg_addQueue, null, array(\ManiaLib\Utils\Formatting::stripCodes($map->name, 'wosnm'), $map->author, \ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm'), $player->login, $queueCount));
        } catch (\Exception $e) {
            $this->exp_chatSendServerMessage(__('Error: %s', $login, $e->getMessage()));
        }
    }

    public function gotoMap($login, \DedicatedApi\Structures\Map $map) {
        try {
            $player = $this->storage->getPlayerObject($login);
            $this->connection->chooseNextMap($map->fileName);
             $map = $this->connection->getNextMapInfo();
            if ($this->config->showNextMapWidget) {
                NextMapWidget::EraseAll();
                $widget = NextMapWidget::Create(null);
                $widget->setPosition(136, 74);
                $widget->setMap($map);
                $widget->show();
            }
            $this->connection->nextMap();
            $this->exp_chatSendServerMessage($this->config->msg_queueNow, null, array(\ManiaLib\Utils\Formatting::stripCodes($map->name, 'wosnm'), $map->author, \ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm'), $login));
        } catch (\Exception $e) {
            $this->exp_chatSendServerMessage(__('Error: %s', $login, $e->getMessage()));
        }
    }

    public function removeMap($login, \DedicatedApi\Structures\Map $map) {
        if (!\ManiaLive\Features\Admin\AdminGroup::contains($login)) {
            $msg = exp_getMessage('#admin_error# $iYou are not allowed to do that!');
            $this->exp_chatSendServerMessage($msg, $login);
            return;
        }

        try {
            $player = $this->storage->getPlayerObject($login);
            $msg = exp_getMessage('#admin_action#Admin #variable#%1$s #admin_action#removed the map #variable#%3$s #admin_action# from the playlist');
            $this->exp_chatSendServerMessage($msg, null, array(\ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm'), $login, \ManiaLib\Utils\Formatting::stripCodes($map->name, 'wosnm'), $map->author));
            $this->connection->removeMap($map->fileName);
        } catch (\Exception $e) {
            $this->exp_chatSendServerMessage(__("Error: %s", $login, $e->getMessage()));
        }
    }

    public function onMapListModified($curMapIndex, $nextMapIndex, $isListModified) {

        if (count($this->queue) > 0) {
            $queue = reset($this->queue);
            $this->nextMap = $queue->map;
        } else {
            $this->nextMap = $this->storage->nextMap;
        }

        if ($this->config->showNextMapWidget) {
            foreach (NextMapWidget::getAll() as $widget) {
                $widget->setMap($this->nextMap);
                $widget->redraw($widget->getRecipient());
            }
        }

        if ($isListModified) {
            $windows = Gui\Windows\Maplist::GetAll();

            foreach ($windows as $window) {
                $login = $window->getRecipient();
                $this->showMapList($login);
            }
        }
    }

    function preloadHistory () {
        $mapList = $this->connection->getMapList(-1, 0);
        $mapCount = count($mapList);
        if ( $mapCount == 0 )
            return;

        $currentMapIndex = $this->connection->getCurrentMapIndex();
        $i = $currentMapIndex - 1;
        $this->history = array();
        
        $endIndex = 9;
        if(sizeof($mapList) < 9)
            $endIndex = sizeof($mapList);
        for ($j = 0; $j < $endIndex; $j++) {
            if(isset($mapList[$i])){
                $this->history[] = $mapList[$i];
            }
            $i--;
            if ( $i < 0 ) {
                $i = $mapCount - 1;
            }
        }
        array_unshift($this->history, $this->storage->currentMap);
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

    function chat_nextMap ($login = null) {
        if ($login != null) {
            if (count($this->queue) > 0) {
                $queue = reset($this->queue);
                $this->exp_chatSendServerMessage($this->config->msg_nextQueue, $login, array(\ManiaLib\Utils\Formatting::stripCodes($queue->map->name, 'wosnm'), $queue->map->author, \ManiaLib\Utils\Formatting::stripCodes($queue->player->nickName, 'wosnm'), $queue->player->login));
            } else {
                $this->exp_chatSendServerMessage($this->config->msg_nextMap, $login, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->nextMap->name, 'wosnm'), $this->storage->nextMap->author));
            }
        }
    }

    function chat_dropQueue ($login = null) {
        if ($login != null) {
            if (count($this->queue) > 0) {
                $player = $this->storage->getPlayerObject($login);
                $i = 0;
                foreach ($this->queue as $queue) {
                    if ($queue->player == $player) {
                        array_splice($this->queue, $i, 1);
                        $msg = exp_getMessage('#variable#%1$s #queue#removed #variable#%2$s #queue#from the queue..');
                        $this->exp_chatSendServerMessage($msg, null, array(\ManiaLib\Utils\Formatting::stripCodes($queue->player->nickName, 'wosnm'), \ManiaLib\Utils\Formatting::stripCodes($queue->map->name, 'wosnm')));
                        break;
                    }
                    $i++;
                }
            } else {
                return;
            }
            if (count($this->queue) > 0) {
                $queue = reset($this->queue);
                $this->nextMap = $queue->map;
            } else {
                $this->nextMap = $this->storage->nextMap;
            }
            if ($this->config->showNextMapWidget) {
                NextMapWidget::EraseAll();
                $widget = NextMapWidget::Create(null);
                $widget->setPosition(136, 74);
                $widget->setMap($this->nextMap);
                $widget->show();
            }
        }
    }

    function emptyWishes($login){
        $player = $this->storage->getPlayerObject($login);
        $this->queue = array();
        $this->nextMap = $this->storage->nextMap;

        if ($this->config->showNextMapWidget) {
            NextMapWidget::EraseAll();
            $widget = NextMapWidget::Create(null);
            $widget->setPosition(136, 74);
            $widget->setMap($this->nextMap);
            $widget->show();
        }

        $msg = exp_getMessage('#admin_action#Admin #variable#%1$s #admin_action#emptied the map queue list');
        $this->exp_chatSendServerMessage($msg, null, array(\ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm'), $login));
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

