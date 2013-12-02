<?php

namespace ManiaLivePlugins\eXpansion\Maps;

use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\Maps\Config;
use ManiaLivePlugins\eXpansion\Maps\Structures\MapWish;
use ManiaLivePlugins\eXpansion\Maps\Gui\Widgets\NextMapWidget;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;

class Maps extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $config;

    /** var MapWish[] */
    private $queue = array();
    private $history = array();
    private $nextMap;
    private $tries = 0;
    private $atPodium = false;
    private $instantReplay = false;
    private $messages;

    /** @var MapWish */
    private $voteItem;
    private $msg_addQueue;
    private $msg_nextQueue;
    private $msg_nextMap;
    private $msg_queueNow;
    private $msg_jukehelp;
    private $msg_errDwld;
    private $msg_errMxId;
    private $msg_mapAdd;

    public function exp_onInit() {

//Oliverde8 Menu
	if ($this->isPluginLoaded('oliverde8\HudMenu')) {
	    Dispatcher::register(\ManiaLivePlugins\oliverde8\HudMenu\onOliverde8HudMenuReady::getClass(), $this);
	}

	$this->messages = new \StdClass();

	$this->config = Config::getInstance();
	$this->config->bufferSize = $this->config->bufferSize + 1;

	$this->setPublicMethod("queueMap");
	$this->setPublicMethod("queueMxMap");
	$this->setPublicMethod("replayMap");
	$this->setPublicMethod("replayMapInstant");
	$this->setPublicMethod("returnQueue");
    }

    public function exp_onReady() {

	$cmd = AdminGroups::addAdminCommand('map remove', $this, 'chat_removeMap', 'map_remove');
	$cmd->setHelp(exp_getMessage('Removes current map from the playlist.'));
	$cmd->setMinParam(1);
	AdminGroups::addAlias($cmd, "remove");

	$cmd = AdminGroups::addAdminCommand('map erase', $this, 'chat_eraseMap', 'map_erease');
	$cmd->setHelp(exp_getMessage('Removes current map from the playlist.'));
	$cmd->setMinParam(0);
	AdminGroups::addAlias($cmd, "nuke this");
	AdminGroups::addAlias($cmd, "trash this");

	$cmd = AdminGroups::addAdminCommand('replaymap', $this, 'replayMap', 'map_res');
	$cmd->setHelp(exp_getMessage('Sets current challenge to replay at end of match'));
	$cmd->setMinParam(0);
	AdminGroups::addAlias($cmd, "replay");

	/*$cmd = AdminGroups::addAdminCommand('map add', $this, 'addMxMap', 'map_add');
	$cmd->setHelp(exp_getMessage('adds a map via MX'));
	$cmd->setMinParam(1);
	AdminGroups::addAlias($cmd, "add");*/

	$this->registerChatCommand('list', "showMapList", 0, true);
	$this->registerChatCommand('maps', "showMapList", 0, true);

	$this->registerChatCommand('nextmap', "chat_nextMap", 0, true);

	$this->registerChatCommand('jb', "jukebox", 1, true);
//$this->registerChatCommand('history', "chat_history", 0, true);
//$this->registerChatCommand('queue', "chat_showQueue", 0, true);

	if ($this->isPluginLoaded('eXpansion\Menu')) {
	    $this->callPublicMethod('eXpansion\Menu', 'addSeparator', __('Maps'), false);
	    $this->callPublicMethod('eXpansion\Menu', 'addItem', __('List maps'), null, array($this, 'showMapList'), false);
	    $this->callPublicMethod('eXpansion\Menu', 'addItem', __('Jukebox'), null, array($this, 'showJukeList'), false);
	    $this->callPublicMethod('eXpansion\Menu', 'addItem', __('Add map'), null, array($this, 'addMaps'), true);
	}

	if ($this->isPluginLoaded('Standard\Menubar')) {
	    $this->buildMenu();
	}

	$this->nextMap = $this->storage->nextMap;

	Gui\Windows\Maplist::Initialize($this);
	Gui\Windows\Jukelist::$mainPlugin = $this;

	foreach ($this->storage->players as $player)
	    $this->onPlayerConnect($player->login, false);
	foreach ($this->storage->spectators as $player)
	    $this->onPlayerConnect($player->login, true);

	$this->preloadHistory();
    }

    public function exp_onLoad() {
	$this->msg_addQueue = exp_getMessage('#variable#%1$s  #queue#has been added to the map queue by #variable#%3$s#queue#, in the #variable#%5$s #queue#position');  // '%1$s' = Map Name, '%2$s' = Map author %, '%3$s' = nickname, '%4$s' = login, '%5$s' = # in queue
	$this->msg_nextQueue = exp_getMessage('#queue#Next map will be #variable#%1$s  #queue#by #variable#%2$s#queue#, as requested by #variable#%3$s');  // '%1$s' = Map Name, '%2$s' = Map author %, '%3$s' = nickname, '%4$s' = login
	$this->msg_nextMap = exp_getMessage('#queue#Next map will be #variable#%1$s  #queue#by #variable#%2$s#queue#');  // '%1$s' = Map Name, '%2$s' = Map author
	$this->msg_queueNow = exp_getMessage('#queue#Map changed to #variable#%1$s  #queue#by #variable#%2$s#queue#, as requested by #variable#%3$s');  // '%1$s' = Map Name, '%2$s' = Map author %, '%3$s' = nickname, '%4$s' = login
	$this->msg_jukehelp = exp_getMessage('/jb uses next params: drop, reset and show');
	$this->msg_errDwld = exp_getMessage('#admin_error#Error downloading, or MX is down!');
	$this->msg_errToLarge = exp_getMessage('#admin_error#The map is to large to be added to a server');
	$this->msg_errMxId = exp_getMessage("#admin_error#You must include a MX map ID!");
	$this->msg_mapAdd = exp_getMessage('#admin_action#Map #variable# %1$s #admin_action#added to playlist by #variable#%2$s');
	$this->enableDedicatedEvents();
    }

    /**
     * 
     * @return boolean
     */
    public function isLocalRecordsLoaded() {
	return $this->isPluginLoaded('eXpansion\LocalRecords');
    }

    /**
     * showRec($login, $map)
     * @param string $login
     * @param \DedicatedApi\Structures\Map $map
     */
    public function showRec($login, $map) {
	$this->callPublicMethod("eXpansion\LocalRecords", "showRecsWindow", $login, $map);
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

    private function hudMenuAdminButtons($menu) {

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
	$button["permission"] = "map_remove";
	$menu->addButton($parent, "Remove Current Map", $button);

	$button["style"] = "Icons64x64_1";
	$button["substyle"] = "Sub";

	$button["plugin"] = $this;
	$button["function"] = "emptyWishes";
	$button["params"] = "this";
	$button["permission"] = "map_jukebox";
	$menu->addButton($parent, "Empty Wish List", $button);

	$button["style"] = "Icons128x128_1";
	$button["substyle"] = "NewTrack";
	$button["function"] = 'addMaps';
	$button["permission"] = "map_add";
	$menu->addButton($parent, "Add Map", $button);

	$button["style"] = "Icons64x64_1";
	$button["substyle"] = "Refresh";
	$button["function"] = 'replayMap';
	$button["permission"] = "map_res";
	$parent = $menu->findButton(array('admin', 'Basic Commands'));
	if (!$parent) {
	    $parent = $menu->findButton(array('admin', 'Maps'));  // no basic cmd submenu?  just dump it in with map cmd's..
	}
	$menu->addButton($parent, "Replay Map", $button);
    }

    function onPlayerConnect($login, $isSpectator) {
	if ($this->config->showNextMapWidget) {
	    $info = \ManiaLivePlugins\eXpansion\Maps\Gui\Widgets\NextMapWidget::Create($login);
	    $info->setPosition(136, 74);
	    $info->setLayer(\ManiaLive\Gui\Window::LAYER_NORMAL);
	    $info->setMap($this->nextMap);
	    $info->show();
	}
    }

    public function onPlayerDisconnect($login, $reason = null) {
	Gui\Windows\Maplist::Erase($login);
	Gui\Windows\AddMaps::Erase($login);
	if ($this->config->showNextMapWidget) {
	    NextMapWidget::Erase($login);
	}
    }

// changed from onBeginMap -> it doesn't trigger if map was replayed.    
    function onBeginMatch() {
	$this->atPodium = false;

	if (count($this->queue) > 0) {
	    reset($this->queue);
	    $queue = current($this->queue);
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
	    reset($this->queue);
	    $queue = current($this->queue);
	    $this->nextMap = $queue->map;
	} else {
	    $this->nextMap = $this->storage->nextMap;
	}

	array_unshift($this->history, $this->storage->currentMap);
	if (count($this->history) > 10) {
	    array_pop($this->history);
	}

	if ($this->config->showNextMapWidget) {
	    $this->redrawNextMapWidget();
	}
    }

    public function onEndMatch($rankings, $winnerTeamOrMap) {

	$this->atPodium = true;

	if (count($this->queue) > 0) {
	    reset($this->queue);
	    $queue = current($this->queue);
//if ($queue->map != $this->storage->nextMap) {
	    try {
		$this->connection->chooseNextMap($queue->map->fileName);
		if ($this->config->showEndMatchNotices) {
		    $this->exp_chatSendServerMessage($this->msg_nextQueue, null, array(\ManiaLib\Utils\Formatting::stripCodes($queue->map->name, 'wosnm'), $queue->map->author, \ManiaLib\Utils\Formatting::stripCodes($queue->player->nickName, 'wosnm'), $queue->player->login));
		}
	    } catch (\Exception $e) {
		$this->exp_chatSendServerMessage(__('Error: %s', $login, $e->getMessage()));
	    }
//}
	} else {
	    if ($this->config->showEndMatchNotices) {
		$map = $this->storage->nextMap;
		if ($this->instantReplay == true) {
		    $this->instantReplay = false;
		    $map = $this->storage->currentMap;
		}
		$this->exp_chatSendServerMessage($this->msg_nextMap, null, array(\ManiaLib\Utils\Formatting::stripCodes($map->name, 'wosnm'), $map->author));
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

    public function jukebox($login, $args = "") {
	echo $args;
	try {
	    switch (strtolower($args)) {
		case "drop":
		    $this->chat_dropQueue($login);
		    break;
		case "reset":
		    AdminGroups::hasPermission($login, "map_jukebox");
		    $this->emptyWishes($login);
		    break;
		case "list":
		case "show":
		    $this->showJukeList($login);
		    break;
		default:
		    $this->exp_chatSendServerMessage($this->msg_jukehelp, $login);
		    break;
	    }
	} catch (\Exception $e) {
	    echo $e->getFile() . ":" . $e->getLine();
	}
    }

    public function showJukeList($login) {
	$window = Gui\Windows\Jukelist::Create($login);
	$window->setList($this->queue);
	$window->centerOnScreen();
	$window->setTitle(__("Jukebox", $login));
	$window->setSize(180, 100);
	$window->show();
    }

    public function showMapList($login) {
	Gui\Windows\Maplist::Erase($login);
	$window = Gui\Windows\Maplist::Create($login);
	$window->setTitle(__('Maps on server', $login));
	if ($this->isPluginLoaded('eXpansion\LocalRecords')) {
	    $window->setRecords($this->callPublicMethod('eXpansion\LocalRecords', 'getPlayersRecordsForAllMaps', $login));
	}
	if ($this->isPluginLoaded('eXpansion\MapRatings')) {
	    $window->setRatings($this->callPublicMethod('eXpansion\MapRatings', 'getRatings'));
	}

	$window->centerOnScreen();
	$window->setSize(180, 120);
	$window->updateList($login);
	$window->show();
    }

    public function queueMap($login, \DedicatedApi\Structures\Map $map, $isTemp = false) {
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

		if (!\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, 'map_jukebox') && $queue->player->login == $login) {
		    $msg = exp_getMessage('#admin_error# $iYou already have a map in the queue...');
		    $this->exp_chatSendServerMessage($msg, $login);
		    return;
		}
	    }

	    if (!\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, 'map_jukebox') && $this->config->bufferSize > 0) {
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
		    $this->redrawNextMapWidget();
		}
//$this->connection->chooseNextMap($map->fileName);
	    }
	    if ($queueCount <= 31) {
		$queueCount = date('jS', strtotime('2007-01-' . $queueCount));
	    }

	    $this->exp_chatSendServerMessage($this->msg_addQueue, null, array(\ManiaLib\Utils\Formatting::stripCodes($map->name, 'wosnm'), $map->author, \ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm'), $player->login, $queueCount));
	} catch (\Exception $e) {
	    $this->exp_chatSendServerMessage(__('Error: %s', $login, $e->getMessage()));
	}
    }

    public function redrawNextMapWidget() {
	foreach ($this->storage->players as $player)
	    $this->onPlayerConnect($player->login, false);
	foreach ($this->storage->spectators as $player)
	    $this->onPlayerConnect($player->login, true);
    }

    public function queueMxMap($login, $file) {
	try {
	    $this->connection->addMap($file);
	    $player = $this->storage->getPlayerObject($login);
	    $map = $this->connection->getMapInfo($file);

	    $this->queue[] = new MapWish($player, $map, true);

	    $queueCount = count($this->queue);
	    if ($queueCount == 1) {
		$this->nextMap = $map;
		if ($this->config->showNextMapWidget) {
		    $this->redrawNextMapWidget();
		}
//$this->connection->chooseNextMap($map->fileName);
	    }
	    if ($queueCount <= 31) {
		$queueCount = date('jS', strtotime('2007-01-' . $queueCount));
	    }

	    $this->exp_chatSendServerMessage($this->msg_addQueue, null, array(\ManiaLib\Utils\Formatting::stripCodes($map->name, 'wosnm'), $map->author, \ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm'), $player->login, $queueCount));
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
		$this->redrawNextMapWidget();
	    }
	    $this->connection->nextMap();
	    $this->exp_chatSendServerMessage($this->msg_queueNow, null, array(\ManiaLib\Utils\Formatting::stripCodes($map->name, 'wosnm'), $map->author, \ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm'), $login));
	} catch (\Exception $e) {
	    $this->exp_chatSendServerMessage(__('Error: %s', $login, $e->getMessage()));
	}
    }

    public function removeMap($login, \DedicatedApi\Structures\Map $map) {
	if (!\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, 'map_remove')) {
	    $msg = exp_getMessage('#admin_error# $iYou are not allowed to do that!');
	    $this->exp_chatSendServerMessage($msg, $login);
	    return;
	}

	try {
	    $player = $this->storage->getPlayerObject($login);
	    $msg = exp_getMessage('#admin_action#Admin #variable#%1$s #admin_action#removed the map #variable#%3$s #admin_action# from the playlist');
	    $this->exp_chatSendServerMessage($msg, null, array(\ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm'), null, \ManiaLib\Utils\Formatting::stripCodes($map->name, 'wosnm'), $map->author));
	    $this->connection->removeMap($map->fileName);
	} catch (\Exception $e) {
	    $this->exp_chatSendServerMessage(__("Error: %s", $login, $e->getMessage()));
	}
    }

    public function eraseMap($login, \DedicatedApi\Structures\Map $map) {
	if (!\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, 'map_erease')) {
	    $msg = exp_getMessage('#admin_error# $iYou are not allowed to do that!');
	    $this->exp_chatSendServerMessage($msg, $login);
	    return;
	}

	try {
	    $player = $this->storage->getPlayerObject($login);
	    $msg = exp_getMessage('#admin_action#Admin #variable#%1$s #admin_action#erased the map #variable#%3$s #admin_action# from playlist and from disk!');
	    $this->exp_chatSendServerMessage($msg, null, array(\ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm'), null, \ManiaLib\Utils\Formatting::stripCodes($map->name, 'wosnm'), $map->author));
	    foreach ($this->storage->maps as $storagemap) {
		if ($storagemap->uId == $map->uId) {
		    $this->connection->removeMap($map->fileName);
		}
	    }

	    unlink($this->connection->getMapsDirectory() . "/" . $map->fileName);
	} catch (\Exception $e) {
	    $this->exp_chatSendServerMessage(__("Error: %s", $login, $e->getMessage()));
	}
    }

    public function onMapListModified($curMapIndex, $nextMapIndex, $isListModified) {
	if (count($this->queue) > 0) {
	    reset($this->queue);
	    $queue = current($this->queue);
	    $this->nextMap = $queue->map;
	} else {
	    $this->nextMap = $this->storage->nextMap;
	}
	// update all widgets
	if ($this->config->showNextMapWidget) {
	    foreach (NextMapWidget::getAll() as $widget) {
		$widget->setMap($this->nextMap);
		$widget->redraw($widget->getRecipient());
	    }
	}
	// update all open Maplist windows 
	if ($isListModified) {
	    $windows = Gui\Windows\Maplist::GetAll();

	    foreach ($windows as $window) {
		$login = $window->getRecipient();
		$this->showMapList($login);
	    }
	}
    }

    public function returnQueue() {
	return $this->queue;
    }

    function preloadHistory() {
	$mapList = $this->connection->getMapList(-1, 0);
	$mapCount = count($mapList);
	if ($mapCount == 0) {
	    return;
	}

	$currentMapIndex = $this->connection->getCurrentMapIndex();
	$i = $currentMapIndex - 1;
	$this->history = array();

	$endIndex = 9;
	if (sizeof($mapList) < 9) {
	    $endIndex = sizeof($mapList);
	}
	for ($j = 0; $j < $endIndex; $j++) {
	    if (isset($mapList[$i])) {
		$this->history[] = $mapList[$i];
	    }
	    $i--;
	    if ($i < 0) {
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

    function chat_eraseMap($login, $params) {
	try {
	    $this->eraseMap($login, $this->storage->currentMap);
	} catch (\Exception $e) {
	    $this->exp_chatSendServerMessage(__("Error: %s", $login, $e->getMessage()));
	}
    }

    function chat_nextMap($login = null) {
	if ($login != null) {
	    if (count($this->queue) > 0) {
		reset($this->queue);
		$queue = current($this->queue);
		$this->exp_chatSendServerMessage($this->msg_nextQueue, $login, array(\ManiaLib\Utils\Formatting::stripCodes($queue->map->name, 'wosnm'), $queue->map->author, \ManiaLib\Utils\Formatting::stripCodes($queue->player->nickName, 'wosnm'), $queue->player->login));
	    } else {
		$this->exp_chatSendServerMessage($this->msg_nextMap, $login, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->nextMap->name, 'wosnm'), $this->storage->nextMap->author));
	    }
	}
    }

    function dropQueue($login, $map) {
	if (!AdminGroups::hasPermission($login, "map_jukebox")) {
	    $this->exp_chatSendServerMessage(AdminGroups::GetnoPermissionMsg(), $login);
	    return;
	}
	$i = 0;
	foreach ($this->queue as $queue) {
	    if ($queue->map->uId == $map->uId) {
		array_splice($this->queue, $i, 1);
		$msg = exp_getMessage('#variable#%1$s #queue#removed #variable#%2$s #queue#from the queue..');
		$this->exp_chatSendServerMessage($msg, null, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->getPlayerObject($login)->nickName, 'wosnm'), \ManiaLib\Utils\Formatting::stripCodes($queue->map->name, 'wosnm')));
		$this->showJukeList($login);
		break;
	    }
	    $i++;
	}
	if (count($this->queue) > 0) {
	    reset($this->queue);
	    $queue = current($this->queue);
	    $this->nextMap = $queue->map;
	} else {
	    $this->nextMap = $this->storage->nextMap;
	}
	if ($this->config->showNextMapWidget) {
	    $this->redrawNextMapWidget();
	}
    }

    function chat_dropQueue($login = null) {
	if ($login == null)
	    return;

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
	    reset($this->queue);
	    $queue = current($this->queue);
	    $this->nextMap = $queue->map;
	} else {
	    $this->nextMap = $this->storage->nextMap;
	}
	if ($this->config->showNextMapWidget) {
	    $this->redrawNextMapWidget();
	}
    }

    function emptyWishesGui($login) {
	$this->emptyWishes($login);
	$this->showJukeList($login);
    }

    function emptyWishes($login) {
	if (!AdminGroups::hasPermission($login, "map_jukebox")) {
	    $this->exp_chatSendServerMessage(AdminGroups::GetnoPermissionMsg(), $login);
	    return;
	}
	$player = $this->storage->getPlayerObject($login);
	$this->queue = array();
	$this->nextMap = $this->storage->nextMap;

	if ($this->config->showNextMapWidget) {
	    $this->redrawNextMapWidget();
	}

	$msg = exp_getMessage('#admin_action#Admin #variable#%1$s #admin_action#emptied the map queue list');
	$this->exp_chatSendServerMessage($msg, null, array(\ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm'), $login));
    }

    function replayMapInstant($login) {
	$player = $this->storage->getPlayerObject($login);
	$this->instantReplay = true;
	foreach (NextMapWidget::getAll() as $widget) {
	    $widget->setMap($this->storage->currentMap);
	    $widget->redraw($widget->getRecipient());
	}
	$this->connection->restartMap($this->storage->gameInfos->gameMode == \DedicatedApi\Structures\GameInfos::GAMEMODE_CUP);
    }

    function replayMap($login) {
	$player = $this->storage->getPlayerObject($login);

	if (count($this->queue) > 0) {
	    reset($this->queue);
	    $queue = current($this->queue);
	    if ($queue->map->uId == $this->storage->currentMap->uId) {
		$msg = exp_getMessage('#admin_error# $iChallenge already set to be replayed!');
		$this->exp_chatSendServerMessage($msg, $login, array(\ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm'), $login));
		return;
	    }
	}

	if (!$this->atPodium) {
	    array_unshift($this->queue, new MapWish($player, $this->storage->currentMap, false));
	} else {
	    $this->connection->restartMap($this->storage->gameInfos->gameMode == \DedicatedApi\Structures\GameInfos::GAMEMODE_CUP);
	}

	$msg = exp_getMessage('#queue#Challenge set to be replayed!');
	$this->exp_chatSendServerMessage($msg, null, array(\ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm'), $login));

	if ($this->config->showNextMapWidget && !$this->atPodium) {
	    $this->nextMap = $this->storage->currentMap;
	    $this->redrawNextMapWidget();
	}
    }

    public function addMaps($login) {
	if (!AdminGroups::hasPermission($login, "map_add")) {
	    $this->exp_chatSendServerMessage(AdminGroups::GetnoPermissionMsg(), $login);
	    return;
	}
	$window = Gui\Windows\AddMaps::Create($login);
	$window->setTitle('Add Maps on server');
	$window->centerOnScreen();
	$window->setSize(180, 100);
	$window->show();
    }

    public function addMxMap($login, $params) {
	if (!AdminGroups::hasPermission($login, "map_add")) {
	    $this->exp_chatSendServerMessage(AdminGroups::GetnoPermissionMsg(), $login);
	    return;
	}

	foreach ($params as $param) {

	    if (is_numeric($param) && $param >= 0) {

		$trkid = ltrim($param, '0');
		$remotefile = 'http://tm.mania-exchange.com/tracks/download/' . $trkid;
		$file = file_get_contents($remotefile);

		if ($file === false || $file == -1) {
		    $this->exp_chatSendServerMessage($this->msg_errDwld, $login);
		} else {
		    if (strlen($file) >= 1024 * 1024) {
			$this->exp_chatSendServerMessage($this->msg_errToLarge, $login);
			return;
		    }
		    $game = $this->connection->getVersion();
		    $path = $this->connection->getMapsDirectory() . "/Downloaded/" . $game->titleId . "/" . $trkid . ".Map.Gbx";

		    if (!$lfile = @fopen($path, 'wb')) {
			$this->exp_chatSendServerMessage('#admin_error#Error creating file. Please contact admin.', $login);
		    }
		    if (!fwrite($lfile, $file)) {
			$this->exp_chatSendServerMessage('#admin_error#Error saving file - unable to write data. Please contact admin.', $login);
			fclose($lfile);
			return;
		    }
		    fclose($lfile);

		    try {
			$this->connection->addMap($path);
			$mapinfo = $this->connection->getMapInfo($path);
			$this->exp_chatSendServerMessage($this->msg_mapAdd, null, array($mapinfo->name, $this->storage->getPlayerObject($login)->nickName));
		    } catch (\Exception $e) {
			$this->connection->chatSendServerMessage(__('Error:', $e->getMessage()));
		    }
		}
	    } else {
		$this->exp_chatSendServerMessage($this->msg_errMxId, $login);
	    }
	}
    }

}

?>