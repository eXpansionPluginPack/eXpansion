<?php

namespace ManiaLivePlugins\eXpansion\Maps;

use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\Maps\Config;
use ManiaLivePlugins\eXpansion\Maps\Structures\MapWish;
use ManiaLivePlugins\eXpansion\Maps\Gui\Widgets\NextMapWidget;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;

class Maps extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

	private $config;
	private $donateConfig;

	/** var MapWish[] */
	private $queue = array();

	/** @var \Maniaplanet\DedicatedServer\Structures\Map[] */
	private $history = array();
	private $nextMap;
	private $tries = 0;
	private $atPodium = false;
	private $instantReplay = false;
	private $paymentInProgress = false;
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
	private $wasWarmup = false;
	private $actionShowMapList;
	private $actionShowJukeList;

	/** @var \ManiaLivePlugins\eXpansion\Maps\Structures\MapSortMode[] */
	public static $playerSortModes = array();
	public static $searchTerm = array();

	public function exp_onInit() {

//Oliverde8 Menu
		if ($this->isPluginLoaded('ManiaLivePlugins\oliverde8\HudMenu\HudMenu')) {
			Dispatcher::register(\ManiaLivePlugins\oliverde8\HudMenu\onOliverde8HudMenuReady::getClass(),
					$this);
		}

		$this->messages = new \StdClass();

		$this->config = Config::getInstance();
		$this->config->bufferSize = $this->config->bufferSize + 1;
		$this->donateConfig = \ManiaLivePlugins\eXpansion\DonatePanel\Config::getInstance();

		$this->setPublicMethod("queueMap");
		$this->setPublicMethod("queueMxMap");
		$this->setPublicMethod("replayMap");
		$this->setPublicMethod("replayMapInstant");
		$this->setPublicMethod("returnQueue");
		$this->setPublicMethod("showMapList");
	}

	public function exp_onReady() {

		$cmd = AdminGroups::addAdminCommand('map remove', $this, 'chat_removeMap',
						'map_remove');
		$cmd->setHelp(exp_getMessage('Removes current map from the playlist.'));
		$cmd->setMinParam(1);
		AdminGroups::addAlias($cmd, "remove");

		$cmd = AdminGroups::addAdminCommand('map erase', $this, 'chat_eraseMap',
						'map_erease');
		$cmd->setHelp(exp_getMessage('Removes current map from the playlist.'));
		$cmd->setMinParam(0);
		AdminGroups::addAlias($cmd, "nuke this");
		AdminGroups::addAlias($cmd, "trash this");

		$cmd = AdminGroups::addAdminCommand('replaymap', $this, 'replayMap', 'map_res');
		$cmd->setHelp(exp_getMessage('Sets current challenge to replay at end of match'));
		$cmd->setMinParam(0);
		AdminGroups::addAlias($cmd, "replay");

		/* $cmd = AdminGroups::addAdminCommand('map add', $this, 'addMxMap', 'map_add');
		  $cmd->setHelp(exp_getMessage('adds a map via MX'));
		  $cmd->setMinParam(1);
		  AdminGroups::addAlias($cmd, "add"); */

		$this->registerChatCommand('list', "showMapList", 0, true);
		$this->registerChatCommand('maps', "showMapList", 0, true);
		// $this->registerChatCommand('history', "showHistoryList", 0, true);

		$this->registerChatCommand('nextmap', "chat_nextMap", 0, true);

		$this->registerChatCommand('jb', "jukebox", 0, true);
		$this->registerChatCommand('jb', "jukebox", 1, true);

//$this->registerChatCommand('history', "chat_history", 0, true);
//$this->registerChatCommand('queue', "chat_showQueue", 0, true);

		if ($this->isPluginLoaded('eXpansion\Menu')) {
			$this->callPublicMethod('ManiaLivePlugins\eXpansion\Menu', 'addSeparator',
					__('Maps'), false);
			$this->callPublicMethod('ManiaLivePlugins\eXpansion\Menu', 'addItem',
					__('List maps'), null, array($this, 'showMapList'), false);
			$this->callPublicMethod('ManiaLivePlugins\eXpansion\Menu', 'addItem',
					__('Jukebox'), null, array($this, 'showJukeList'), false);
			$this->callPublicMethod('ManiaLivePlugins\eXpansion\Menu', 'addItem',
					__('Add map'), null, array($this, 'addMaps'), true);
		}

		if ($this->isPluginLoaded('Standard\Menubar')) {
			$this->buildMenu();
		}

		$this->nextMap = $this->storage->nextMap;

		Gui\Windows\Maplist::Initialize($this);
		Gui\Windows\Jukelist::$mainPlugin = $this;
		/** @var \ManiaLive\Gui\ActionHandler */
		$action = \ManiaLive\Gui\ActionHandler::getInstance();
		$this->actionShowMapList = $action->createAction(array($this, "showMapList"));
		$this->actionShowJukeList = $action->createAction(array($this, "showJukeList"));


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
		$this->msg_jukehelp = exp_getMessage('#queue#/jb uses next params: drop, reset and show');
		$this->msg_errDwld = exp_getMessage('#admin_error#Error downloading, or MX is down!');
		$this->msg_errToLarge = exp_getMessage('#admin_error#The map is to large to be added to a server');
		$this->msg_errMxId = exp_getMessage("#admin_error#You must include a MX map ID!");
		$this->msg_mapAdd = exp_getMessage('#admin_action#Map #variable# %1$s #admin_action#added to playlist by #variable#%2$s');
		$this->enableDedicatedEvents();
		$this->console("jukeOnLoad");
	}

	/**
	 * 
	 * @return boolean
	 */
	public function isLocalRecordsLoaded() {
		return $this->isPluginLoaded('ManiaLivePlugins\\eXpansion\\LocalRecords\\LocalRecords');
	}

	/**
	 * showRec($login, $map)
	 * @param string $login
	 * @param \Maniaplanet\DedicatedServer\Structures\Map $map
	 */
	public function showRec($login, $map) {
		$this->callPublicMethod("ManiaLivePlugins\\eXpansion\\LocalRecords\\LocalRecords",
				"showRecsWindow", $login, $map);
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
		\ManiaLive\Gui\CustomUI::HideForAll(\ManiaLive\Gui\CustomUI::CHALLENGE_INFO);

		$info = \ManiaLivePlugins\eXpansion\Maps\Gui\Widgets\CurrentMapWidget::Create($login);
		$info->setPosition(144, 83.5);
		$info->setAction($this->actionShowMapList);
		$info->show();
		$this->showNextMapWidget($login);
	}

	public function onPlayerDisconnect($login, $reason = null) {
		Gui\Windows\Maplist::Erase($login);
		Gui\Windows\AddMaps::Erase($login);
		if (array_key_exists($login, self::$playerSortModes)) {
			unset(self::$playerSortModes[$login]);
		}
		if (array_key_exists($login, self::$searchTerm)) {
			unset(self::$searchTerm[$login]);
		}
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
					}
					catch (\Exception $e) {
						$this->exp_chatSendServerMessage(__("Error: %s", $login, $e->getMessage()));
					}
				}
				array_shift($this->queue);
			}
			else {
				if ($this->tries < 3) {
					$this->tries++;
				}
				else {
					$this->tries = 0;
					array_shift($this->queue);
				}
			}
		}

		if (count($this->queue) > 0) {
			reset($this->queue);
			$queue = current($this->queue);
			$this->nextMap = $queue->map;
		}
		else {
			$this->nextMap = $this->storage->nextMap;
		}

		array_unshift($this->history, $this->storage->currentMap);
		if (count($this->history) > $this->config->historySize) {
			array_pop($this->history);
		}

		foreach ($this->storage->players as $player)
			$this->onPlayerConnect($player->login, false);
		foreach ($this->storage->spectators as $player)
			$this->onPlayerConnect($player->login, true);

		NextMapWidget::EraseAll();
	}

	public function onBeginRound() {
		$this->wasWarmup = $this->connection->getWarmUp();
	}

	public function showNextMapWidget($login) {
		if ($this->config->showNextMapWidget) {
			$info = \ManiaLivePlugins\eXpansion\Maps\Gui\Widgets\NextMapWidget::Create($login);
			$info->setPosition(125, 64);
			$info->setLayer(\ManiaLive\Gui\Window::LAYER_NORMAL);
			$info->setAction($this->actionShowJukeList);
			$info->setMap($this->nextMap);
			$info->show();
		}
	}

	public function onEndMatch($rankings, $winnerTeamOrMap) {
		if ($this->wasWarmup)
			return;

		Gui\Widgets\CurrentMapWidget::EraseAll();

		$this->atPodium = true;

		foreach ($this->storage->players as $player)
			$this->redrawNextMapWidget($player->login, false);
		foreach ($this->storage->spectators as $player)
			$this->redrawNextMapWidget($player->login, true);


		if (count($this->queue) > 0) {
			reset($this->queue);
			$queue = current($this->queue);
//if ($queue->map != $this->storage->nextMap) {
			try {
				$this->connection->chooseNextMap($queue->map->fileName);
				if ($this->config->showEndMatchNotices) {
					$this->exp_chatSendServerMessage($this->msg_nextQueue, null,
							array(\ManiaLib\Utils\Formatting::stripCodes($queue->map->name, 'wosnm'), $queue->map->author, \ManiaLib\Utils\Formatting::stripCodes($queue->player->nickName,
								'wosnm'), $queue->player->login));
				}
			}
			catch (\Exception $e) {
				$this->exp_chatSendServerMessage('Error: %s', $queue->player->login,
						$e->getMessage());
				$key = key($this->queue);
				unset($this->queue[$key]);
				$this->exp_chatSendServerMessage('Recovering from error, map removed from jukebox...',
						$queue->player->login);
			}
//}
		}
		else {
			if ($this->config->showEndMatchNotices) {
				$map = $this->storage->nextMap;
				if ($this->instantReplay == true) {
					$this->instantReplay = false;
					$map = $this->storage->currentMap;
				}
				$this->exp_chatSendServerMessage($this->msg_nextMap, null,
						array(\ManiaLib\Utils\Formatting::stripCodes($map->name, 'wosnm'), $map->author));
			}
		}
	}

	public function buildMenu() {
		$this->callPublicMethod('Standard\Menubar', 'initMenu',
				\ManiaLib\Gui\Elements\Icons128x128_1::Challenge);
		$this->callPublicMethod('Standard\Menubar', 'addButton',
				'List all maps on server', array($this, 'showMapList'), false);
		$this->callPublicMethod('Standard\Menubar', 'addButton',
				'Add local map on server', array($this, 'addMaps'), true);

// user call votes disabled since dedicated doesn't support them atm.
//  $this->callPublicMethod('Standard\Menubar', 'addButton', 'Vote for skip map', array($this, 'voteSkip'), false);
//  $this->callPublicMethod('Standard\Menubar', 'addButton', 'Vote for replay map', array($this, 'voteRestart'), false);
	}

	public function jukebox($login, $args = "") {
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
		}
		catch (\Exception $e) {
			$this->console($e->getFile() . ":" . $e->getLine());
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
		$window->setHistory($this->history);
		$window->setCurrentMap($this->storage->currentMap);
		if ($this->isPluginLoaded('ManiaLivePlugins\eXpansion\LocalRecords\LocalRecords')) {
			$window->setRecords($this->callPublicMethod('ManiaLivePlugins\\eXpansion\\LocalRecords\\LocalRecords',
							'getPlayersRecordsForAllMaps', $login));
		}
		if ($this->isPluginLoaded('ManiaLivePlugins\\eXpansion\\MapRatings\\MapRatings')) {
			$window->setRatings($this->callPublicMethod('ManiaLivePlugins\\eXpansion\\MapRatings\\MapRatings',
							'getRatings'));
		}

		$window->centerOnScreen();
		$window->setSize(180, 100);

		$window->updateList($login);
		$window->show();
	}

	public function showHistoryList($login) {
		Gui\Windows\Maplist::Erase($login);
		$window = Gui\Windows\Maplist::Create($login);
		$window->setHistory($this->history);
		$window->setTitle(__('History of Maps', $login));
		if ($this->isPluginLoaded('ManiaLivePlugins\eXpansion\LocalRecords\LocalRecords')) {
			$window->setRecords($this->callPublicMethod('ManiaLivePlugins\eXpansion\LocalRecords',
							'getPlayersRecordsForAllMaps', $login));
		}
		if ($this->isPluginLoaded('eXpansion\MapRatings')) {
			$window->setRatings($this->callPublicMethod('ManiaLivePlugins\eXpansion\MapRatings',
							'getRatings'));
		}

		$window->centerOnScreen();
		$wind6ow->setSize(180, 100);
		$window->updateList($login, 'name', 'null', $this->history);
		$window->show();
	}

	/**
	 * Returns the amount of planets that needs to be payed to wish a map
	 * @return int
	 * 		0 : if for free
	 * 		-1 : if queu is full
	 * 		X ; the ammount to pay
	 */
	public function getQueuAmount() {
		if (!empty($this->config->publicQueuAmount) && $this->config->publicQueuAmount != -1) {
			if (isset($this->config->publicQueuAmount[sizeof($this->queue)])) {
				$amount = $this->config->publicQueuAmount[sizeof($this->queue)];
				return $amount != -1 ? $amount : 0;
			}
			return -1; //Impossible
		}
		return 0;
	}

	public function playerQueueMap($login,
			\Maniaplanet\DedicatedServer\Structures\Map $map, $isTemp = false) {
		
		$amount = $this->getQueuAmount();
		
		if ($amount == 0 || AdminGroups::hasPermission($login, 'map_jukebox_free'))
			$this->queueMap($login, $map, $isTemp);
		else if ($amount != -1) {
			if ($this->checkQueuMap($login, $map, true)) {

				if ($this->paymentInProgress) {
					$msg = exp_getMessage('#admin_error# $iA payment for wishin a track is in progress please try later.');
					$this->exp_chatSendServerMessage($msg, $login);
					return;
				}

				//Start Bill
				$this->paymentInProgress = true;

				if (!empty($this->donateConfig->toLogin))
					$toLogin = $this->donateConfig->toLogin;
				else
					$toLogin = $this->storage->serverLogin;

				$bill = $this->exp_startBill($login, $toLogin, $amount,
						__("Are you sure you want to wish this map to be played", $login),
						array($this, 'validateQueuMap'));
				
				$bill->setSubject('map_wish');
				$bill->setErrorCallback(5, array($this, 'failQueuMap'));
				$bill->setErrorCallback(6, array($this, 'failQueuMap'));
				$bill->map = $map;
			}
		}else{
			$msg = exp_getMessage('#admin_error# $iYOu can\'t wish for a map at the moment.');
			$this->exp_chatSendServerMessage($msg, $login);
		}
	}

	public function validateQueuMap(\ManiaLivePlugins\eXpansion\Core\types\Bill $bill) {
		$this->paymentInProgress = false;
		$this->queueMap($bill->getSource_login(), $bill->map, false, false);
	}

	public function failQueuMap(\ManiaLivePlugins\eXpansion\Core\types\Bill $bill,
			$state, $stateName) {
		$this->paymentInProgress = false;
	}

	public function checkQueuMap($login,
			\Maniaplanet\DedicatedServer\Structures\Map $map, $sendMessages = false) {

		$player = $this->storage->getPlayerObject($login);

		if ($this->storage->currentMap->uId == $map->uId) {
			$msg = exp_getMessage('#admin_error# $iThis map is currently playing...');
			if ($sendMessages)
				$this->exp_chatSendServerMessage($msg, $login);
			return false;
		}

		foreach ($this->queue as $queue) {
			if ($queue->map->uId == $map->uId) {
				$msg = exp_getMessage('#admin_error# $iThis map is already in the queue...');
				if ($sendMessages)
					$this->exp_chatSendServerMessage($msg, $login);
				return false;
			}

			if (!\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login,
							'map_jukebox') && $queue->player->login == $login) {
				$msg = exp_getMessage('#admin_error# $iYou already have a map in the queue...');
				if ($sendMessages)
					$this->exp_chatSendServerMessage($msg, $login);
				return false;
			}
		}

		if (!\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login,
						'map_jukebox') && $this->config->bufferSize > 0) {
			$i = 0;

			for ($i = 0; $i <= $this->config->bufferSize; $i++) {
				$cp = sizeof($this->history) - 1 - $i;
				if (isset($this->history[$cp])) {
					if ($this->history[$cp]->uId == $map->uId) {
						$msg = exp_getMessage('#admin_error# $iMap has been played too recently...');
						if ($sendMessages)
							$this->exp_chatSendServerMessage($msg, $login);
						return false;
					}
				}
			}
		}
		return true;
	}

	public function queueMap($login,
			\Maniaplanet\DedicatedServer\Structures\Map $map, $isTemp = false,
			$check = true) {
		
		$player = $this->storage->getPlayerObject($login);
		
		try {
			if ($check && !$this->checkQueuMap($login, $map, true))
				return;

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

			$this->exp_chatSendServerMessage($this->msg_addQueue, null,
					array(\ManiaLib\Utils\Formatting::stripCodes($map->name, 'wosnm'), $map->author, \ManiaLib\Utils\Formatting::stripCodes($player->nickName,
						'wosnm'), $player->login, $queueCount));
		}
		catch (\Exception $e) {
			$this->exp_chatSendServerMessage(__('Error: %s', $login, $e->getMessage()));
		}
	}

	public function redrawNextMapWidget() {
		foreach ($this->storage->players as $player)
			$this->showNextMapWidget($player->login, false);
		foreach ($this->storage->spectators as $player)
			$this->showNextMapWidget($player->login, true);
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

			$this->exp_chatSendServerMessage($this->msg_addQueue, null,
					array(\ManiaLib\Utils\Formatting::stripCodes($map->name, 'wosnm'), $map->author, \ManiaLib\Utils\Formatting::stripCodes($player->nickName,
						'wosnm'), $player->login, $queueCount));
		}
		catch (\Exception $e) {
			$this->exp_chatSendServerMessage(__('Error: %s', $login, $e->getMessage()));
		}
	}

	public function gotoMap($login,
			\Maniaplanet\DedicatedServer\Structures\Map $map) {
		try {

			$player = $this->storage->getPlayerObject($login);

			$this->connection->chooseNextMap($map->fileName);
			$map = $this->connection->getNextMapInfo();
			if ($this->config->showNextMapWidget) {
				$this->redrawNextMapWidget();
			}
			$this->connection->nextMap();
			$this->exp_chatSendServerMessage($this->msg_queueNow, null,
					array(\ManiaLib\Utils\Formatting::stripCodes($map->name, 'wosnm'), $map->author, \ManiaLib\Utils\Formatting::stripCodes($player->nickName,
						'wosnm'), $login));
		}
		catch (\Exception $e) {
			$this->exp_chatSendServerMessage(__('Error: %s', $login, $e->getMessage()));
		}
	}

	public function removeMap($login,
			\Maniaplanet\DedicatedServer\Structures\Map $map) {
		if (!\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login,
						'map_remove')) {
			$msg = exp_getMessage('#admin_error# $iYou are not allowed to do that!');
			$this->exp_chatSendServerMessage($msg, $login);
			return;
		}

		try {
			$player = $this->storage->getPlayerObject($login);
			$msg = exp_getMessage('#admin_action#Admin #variable#%1$s #admin_action#removed the map #variable#%3$s #admin_action# from the playlist');
			$this->exp_chatSendServerMessage($msg, null,
					array(\ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm'), null, \ManiaLib\Utils\Formatting::stripCodes($map->name,
						'wosnm'), $map->author));
			$this->connection->removeMap($map->fileName);
		}
		catch (\Exception $e) {
			$this->exp_chatSendServerMessage(__("Error: %s", $login, $e->getMessage()));
		}
	}

	public function eraseMap($login,
			\Maniaplanet\DedicatedServer\Structures\Map $map) {
		if (!\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login,
						'map_erease')) {
			$msg = exp_getMessage('#admin_error# $iYou are not allowed to do that!');
			$this->exp_chatSendServerMessage($msg, $login);
			return;
		}

		try {
			$player = $this->storage->getPlayerObject($login);
			$msg = exp_getMessage('#admin_action#Admin #variable#%1$s #admin_action#erased the map #variable#%3$s #admin_action# from playlist and from disk!');
			$this->exp_chatSendServerMessage($msg, null,
					array(\ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm'), null, \ManiaLib\Utils\Formatting::stripCodes($map->name,
						'wosnm'), $map->author));
			foreach ($this->storage->maps as $storagemap) {
				if ($storagemap->uId == $map->uId) {
					$this->connection->removeMap($map->fileName);
				}
			}

			unlink($this->connection->getMapsDirectory() . "/" . $map->fileName);
		}
		catch (\Exception $e) {
			$this->exp_chatSendServerMessage(__("Error: %s", $login, $e->getMessage()));
		}
	}

	public function onMapListModified($curMapIndex, $nextMapIndex, $isListModified) {
		if (count($this->queue) > 0) {
			reset($this->queue);
			$queue = current($this->queue);
			$this->nextMap = $queue->map;
		}
		else {
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

		$endIndex = $this->config->historySize - 1;
		if (sizeof($mapList) < $this->config->historySize - 1) {
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
		}
		catch (\Exception $e) {
			$this->exp_chatSendServerMessage(__("Error: %s", $login, $e->getMessage()));
		}
	}

	function chat_nextMap($login = null) {
		if ($login != null) {
			if (count($this->queue) > 0) {
				reset($this->queue);
				$queue = current($this->queue);
				$this->exp_chatSendServerMessage($this->msg_nextQueue, $login,
						array(\ManiaLib\Utils\Formatting::stripCodes($queue->map->name, 'wosnm'), $queue->map->author, \ManiaLib\Utils\Formatting::stripCodes($queue->player->nickName,
							'wosnm'), $queue->player->login));
			}
			else {
				$this->exp_chatSendServerMessage($this->msg_nextMap, $login,
						array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->nextMap->name,
							'wosnm'), $this->storage->nextMap->author));
			}
		}
	}

	function dropQueue($login, $map) {
		$i = 0;
		foreach ($this->queue as $queue) {
			if ($queue->map->uId == $map->uId) {
				array_splice($this->queue, $i, 1);
				$msg = exp_getMessage('#variable#%1$s #queue#removed #variable#%2$s #queue#from the queue..');
				$this->exp_chatSendServerMessage($msg, null,
						array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->getPlayerObject($login)->nickName,
							'wosnm'), \ManiaLib\Utils\Formatting::stripCodes($queue->map->name,
							'wosnm')));
				$this->showJukeList($login);
				break;
			}
			$i++;
		}
		if (count($this->queue) > 0) {
			reset($this->queue);
			$queue = current($this->queue);
			$this->nextMap = $queue->map;
		}
		else {
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
					$this->exp_chatSendServerMessage($msg, null,
							array(\ManiaLib\Utils\Formatting::stripCodes($queue->player->nickName,
								'wosnm'), \ManiaLib\Utils\Formatting::stripCodes($queue->map->name,
								'wosnm')));
					break;
				}
				$i++;
			}
		}
		else {
			return;
		}
		if (count($this->queue) > 0) {
			reset($this->queue);
			$queue = current($this->queue);
			$this->nextMap = $queue->map;
		}
		else {
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
		$this->exp_chatSendServerMessage($msg, null,
				array(\ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm'), $login));
	}

	function replayMapInstant($login) {
		$this->instantReplay = true;
		foreach (NextMapWidget::getAll() as $widget) {
			$widget->setMap($this->storage->currentMap);
			$widget->redraw($widget->getRecipient());
		}
		$this->connection->restartMap($this->storage->gameInfos->gameMode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP);
	}

	function replayMap($login) {
		$player = $this->storage->getPlayerObject($login);

		if (count($this->queue) > 0) {
			reset($this->queue);
			$queue = current($this->queue);
			if ($queue->map->uId == $this->storage->currentMap->uId) {
				$msg = exp_getMessage('#admin_error# $iChallenge already set to be replayed!');
				$this->exp_chatSendServerMessage($msg, $login,
						array(\ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm'), $login));
				return;
			}
		}

		if (!$this->atPodium) {
			array_unshift($this->queue,
					new MapWish($player, $this->storage->currentMap, false));
		}
		else {
			$this->connection->restartMap($this->storage->gameInfos->gameMode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP);
		}

		$msg = exp_getMessage('#queue#Challenge set to be replayed!');
		$this->exp_chatSendServerMessage($msg, null,
				array(\ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm'), $login));

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
				}
				else {
					if (strlen($file) >= 1024 * 1024) {
						$this->exp_chatSendServerMessage($this->msg_errToLarge, $login);
						return;
					}
					$game = $this->connection->getVersion();
					$path = $this->connection->getMapsDirectory() . "/Downloaded/" . $game->titleId . "/" . $trkid . ".Map.Gbx";

					if (!$lfile = @fopen($path, 'wb')) {
						$this->exp_chatSendServerMessage('#admin_error#Error creating file. Please contact admin.',
								$login);
					}
					if (!fwrite($lfile, $file)) {
						$this->exp_chatSendServerMessage('#admin_error#Error saving file - unable to write data. Please contact admin.',
								$login);
						fclose($lfile);
						return;
					}
					fclose($lfile);

					try {
						$this->connection->addMap($path);
						$mapinfo = $this->connection->getMapInfo($path);
						$this->exp_chatSendServerMessage($this->msg_mapAdd, null,
								array($mapinfo->name, $this->storage->getPlayerObject($login)->nickName));
					}
					catch (\Exception $e) {
						$this->connection->chatSendServerMessage(__('Error:', $e->getMessage()));
					}
				}
			}
			else {
				$this->exp_chatSendServerMessage($this->msg_errMxId, $login);
			}
		}
	}

}

?>