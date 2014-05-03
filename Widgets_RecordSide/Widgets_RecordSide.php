<?php

namespace ManiaLivePlugins\eXpansion\Widgets_RecordSide;

use \ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\LocalRecords\Events\Event as LocalEvent;
use \Maniaplanet\DedicatedServer\Structures\GameInfos;

class Widgets_RecordSide extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    const None = 0x0;
    const Dedimania = 0x2;
    const Localrecords = 0x4;
    const Dedimania_force = 0x8;
    const All = 0x31;

    public static $me = null;
    public static $dedirecords = array();
    public static $localrecords = array();
    public static $secondMap = false;
    private $lastUpdate;
    private $forceUpdate = false;
    private $needUpdate = false;
    private $dedi = true;
    private $local = true;
    private $widgetIds = array();
    public static $raceOn;
    public static $roundPoints;

    /** @var Config */
    private $config;

    public function exp_onInit() {
	$this->exp_addTitleSupport("TM");
	$this->exp_addTitleSupport("Trackmania");
	$this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_ROUNDS);
	$this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK);
	$this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM);
	$this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS);
	$this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP);
	$this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT, 'TeamAttack.Script.txt');
	$this->addDependency(new \ManiaLive\PluginHandler\Dependency('\ManiaLivePlugins\\eXpansion\\LocalRecords\\LocalRecords'));
    }

    public function exp_onLoad() {
	if ($this->isPluginLoaded('\ManiaLivePlugins\\eXpansion\\Dedimania\\Dedimania') || $this->isPluginLoaded('\ManiaLivePlugins\\eXpansion\\Dedimania_Script\\Dedimania_Script'))
	    Dispatcher::register(\ManiaLivePlugins\eXpansion\Dedimania\Events\Event::getClass(), $this);

	Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_RECORDS_LOADED);
	Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_NEW_RECORD);
	Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_UPDATE_RECORDS);
	$this->config = Config::getInstance();
    }

    public function exp_onReady() {
	$this->enableDedicatedEvents();

	/* foreach ($this->storage->players as $player)
	  $this->onPlayerConnect($player->login, false); // create panel for everybody
	  foreach ($this->storage->spectators as $player)
	  $this->onPlayerConnect($player->login, true); // create panel for everybody */


	$this->lastUpdate = time();
	self::$localrecords = $this->callPublicMethod("\\ManiaLivePlugins\\eXpansion\\LocalRecords\\LocalRecords", "getRecords");
	$this->enableTickerEvent();
	$this->needUpdate = self::Localrecords;

	$this->getRoundsPoints();
	$this->updateDediPanel();
	$this->updateLocalPanel();
	$this->updateLivePanel();
	self::$me = $this;
    }

    public function onTick() {

	if ((time() - $this->lastUpdate) > 1 && $this->needUpdate !== false || $this->forceUpdate == true) {

	    if (($this->needUpdate & self::Dedimania) == self::Dedimania || $this->forceUpdate || ($this->needUpdate & self::Dedimania_force) == self::Dedimania_force) {
		if ($this->dedi || $this->needUpdate == self::Dedimania_force) {
		    $this->updateDediPanel();
		    $this->dedi = false;
		}
	    }

	    $this->lastUpdate = time();
	    $this->forceUpdate = false;
	    $this->needUpdate = false;
	}
    }

    public function updateDediPanel($login = NULL) {

	if ($this->isPluginLoaded('\ManiaLivePlugins\\eXpansion\\Dedimania\\Dedimania') || $this->isPluginLoaded('\ManiaLivePlugins\\eXpansion\\Dedimania_Script\\Dedimania_Script')) {
	    if ($login == Null) {
		//Gui\Widgets\DediPanel::EraseAll();
		$panelMain = Gui\Widgets\DediPanel::Create($login);
		$panelMain->setLayer(\ManiaLive\Gui\Window::LAYER_NORMAL);
		$panelMain->setSizeX(40);

		$this->widgetIds["DediPanel"] = $panelMain;
	    } else if (isset($this->widgetIds["DediPanel"])) {
		$this->widgetIds["DediPanel"]->update();
		$this->widgetIds["DediPanel"]->show($login);
	    }

	    if ($login == Null) {
		//Gui\Widgets\DediPanel2::EraseAll();
		$panelScore = Gui\Widgets\DediPanel2::Create($login);
		$panelScore->setLayer(\ManiaLive\Gui\Window::LAYER_SCORES_TABLE);
		$panelScore->setVisibleLayer("scorestable");
		$panelScore->setSizeX(40);
		$this->widgetIds["DediPanel2"] = $panelScore;


		$panelScore->update();
		$panelMain->update();
		$panelMain->show();
		$panelScore->show();
	    } else if (isset($this->widgetIds["DediPanel2"])) {
		$this->widgetIds["DediPanel2"]->update();
		$this->widgetIds["DediPanel2"]->show($login);
	    }
	}
    }

    public function updateLocalPanel($login = null) {

	if ($login == Null) {
	    //Gui\Widgets\LocalPanel::EraseAll();
	    $panelMain = Gui\Widgets\LocalPanel::Create($login);
	    $panelMain->setSizeX(40);
	    $panelMain->setLayer(\ManiaLive\Gui\Window::LAYER_NORMAL);
	    $this->widgetIds["LocalPanel"] = $panelMain;
	} else if (isset($this->widgetIds["LocalPanel"])) {
	    $this->widgetIds["LocalPanel"]->update();
	    $this->widgetIds["LocalPanel"]->show($login);
	}

	if ($login == Null) {
	    //Gui\Widgets\LocalPanel2::EraseAll();
	    $panelScore = Gui\Widgets\LocalPanel2::Create($login);
	    $panelScore->setSizeX(40);
	    $panelScore->setLayer(\ManiaLive\Gui\Window::LAYER_SCORES_TABLE);
	    $panelScore->setVisibleLayer("scorestable");

	    $this->widgetIds["LocalPanel2"] = $panelScore;

	    $panelScore->update();
	    $panelMain->update();
	    $panelMain->show();
	    $panelScore->show();
	} else if (isset($this->widgetIds["LocalPanel2"])) {
	    $this->widgetIds["LocalPanel2"]->update();
	    $this->widgetIds["LocalPanel2"]->show($login);
	}
    }

    public function updateLivePanel($login = null) {
	Gui\Widgets\LivePanel::$connection = $this->connection;

	if ($login == Null) {
	    //Gui\Widgets\LivePanel::EraseAll();
	    $panelMain = Gui\Widgets\LivePanel::Create($login);
	    $panelMain->setLayer(\ManiaLive\Gui\Window::LAYER_NORMAL);
	    $panelMain->setSizeX(40);
	    $this->widgetIds["LivePanel"] = $panelMain;
	} else if (isset($this->widgetIds["LivePanel"])) {
	    $this->widgetIds["LivePanel"]->update();
	    $this->widgetIds["LivePanel"]->show($login);
	}

	if ($login == Null) {
	    //Gui\Widgets\LivePanel2::EraseAll();
	    $panelScore = Gui\Widgets\LivePanel2::Create($login);
	    $panelScore->setLayer(\ManiaLive\Gui\Window::LAYER_SCORES_TABLE);
	    $panelScore->setVisibleLayer("scorestable");
	    $panelScore->setSizeX(40);
	    $this->widgetIds["LivePanel2"] = $panelScore;
	    $panelScore->update();
	    $panelMain->update();
	    $panelMain->show();
	    $panelScore->show();
	} else if (isset($this->widgetIds["LivePanel2"])) {
	    $this->widgetIds["LivePanel2"]->update();
	    $this->widgetIds["LivePanel2"]->show($login);
	}

	$gamemode = self::exp_getCurrentCompatibilityGameMode();
	if ($gamemode == GameInfos::GAMEMODE_ROUNDS || $gamemode == GameInfos::GAMEMODE_TEAM || $gamemode == GameInfos::GAMEMODE_CUP) {
	    if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_SCRIPT) {
		$this->connection->triggerModeScriptEvent("UI_DisplaySmallScoresTable", "False");
	    } else {
		\ManiaLive\Gui\CustomUI::HideForAll(\ManiaLive\Gui\CustomUI::ROUND_SCORES);
	    }
	}
    }

    public function showLocalPanel($login) {
	$this->updateLocalPanel($login);
    }

    public function showDediPanel($login) {
	$this->updateDediPanel($login);
    }

    public function showLivePanel($login) {
	$this->updateLivePanel($login);
    }

    public function hideLivePanel() {
	Gui\Widgets\LivePanel::EraseAll();
	Gui\Widgets\LivePanel2::EraseAll();
    }

    public function onEndMatch($rankings, $winnerTeamOrMap) {

	self::$raceOn = false;
	$this->widgetIds = array();
	Gui\Widgets\LocalPanel::EraseAll();
	Gui\Widgets\LocalPanel2::EraseAll();
	Gui\Widgets\DediPanel::EraseAll();
	Gui\Widgets\DediPanel2::EraseAll();
	$this->hideLivePanel();
    }

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {
	if ($wasWarmUp) {
	    self::$raceOn = false;
	    $this->forceUpdate = true;
	    $this->updateDediPanel();
	    $this->updateLocalPanel();
	    $this->updateLivePanel();
	    self::$secondMap = true;
	    self::$raceOn = true;
	} else {
	    self::$dedirecords = array(); // reset 
	    self::$localrecords = array(); //  reset
	    $this->widgetIds = array();
	    Gui\Widgets\LocalPanel::EraseAll();
	    Gui\Widgets\LocalPanel2::EraseAll();
	    Gui\Widgets\DediPanel::EraseAll();
	    Gui\Widgets\DediPanel2::EraseAll();
	    $this->hideLivePanel();
	}
    }

    public function getRoundsPoints() {
	if ($this->storage->gameInfos->gameMode != GameInfos::GAMEMODE_SCRIPT) {
	    $points = $this->connection->getRoundCustomPoints();
	    if (empty($points)) {
		self::$roundPoints = array(10, 6, 4, 3, 2, 1);
	    } else {
		self::$roundPoints = $points;
	    }
	} else {
	    self::$roundPoints = array(10, 6, 4, 3, 2, 1);
	}
    }

    public function onBeginMap($map, $warmUp, $matchContinuation) {
	$this->getRoundsPoints();
	self::$raceOn = false;
	$this->forceUpdate = true;
	$this->widgetIds = array();
	Gui\Widgets\LocalPanel::EraseAll();
	Gui\Widgets\LocalPanel2::EraseAll();
	Gui\Widgets\DediPanel::EraseAll();
	Gui\Widgets\DediPanel2::EraseAll();
	$this->hideLivePanel();
	$this->updateDediPanel();
	$this->updateLocalPanel();
	$this->updateLivePanel();
	self::$secondMap = true;
	self::$raceOn = true;
    }

    public function onBeginMatch() {
	self::$raceOn = false;
	$this->forceUpdate = true;
	$this->widgetIds = array();
	Gui\Widgets\LocalPanel::EraseAll();
	Gui\Widgets\LocalPanel2::EraseAll();
	Gui\Widgets\DediPanel::EraseAll();
	Gui\Widgets\DediPanel2::EraseAll();
	$this->hideLivePanel();
	$this->updateDediPanel();
	$this->updateLocalPanel();
	$this->updateLivePanel();
	self::$secondMap = true;
	self::$raceOn = true;
    }

    public function onEndRound() {
	//@TOdo remove it is good to have it to keep track of other players
	/*if($this->storage->gameInfos->gameMode != GameInfos::GAMEMODE_ROUNDS){
	    $this->hideLivePanel();
	}*/
    }

    public function onBeginRound() {
	//We need to reset the panel for next Round
	self::$raceOn = false;
	$this->hideLivePanel();
	$this->updateLivePanel();
	self::$raceOn = true;
    }

    public function onRecordsLoaded($data) {
	self::$localrecords = $data;
	$this->local = true;
	$this->needUpdate = self::$localrecords;
    }

    public function onDedimaniaGetRecords($data) {
	self::$dedirecords = $data['Records'];
	$this->dedi = True;
	$this->needUpdate = self::Dedimania_force;
    }

    public function onPlayerConnect($login, $isSpectator) {
	$this->showLocalPanel($login);
	$this->showDediPanel($login);
	$this->showLivePanel($login);
    }

    public function onPlayerDisconnect($login, $reason = null) {
	Gui\Widgets\LocalPanel::Erase($login);
	Gui\Widgets\DediPanel::Erase($login);
	Gui\Widgets\LocalPanel2::Erase($login);
	Gui\Widgets\DediPanel2::Erase($login);
	Gui\Widgets\LivePanel::Erase($login);
	Gui\Widgets\LivePanel2::Erase($login);
    }

    public function onDedimaniaOpenSession() {
	
    }

    public function onNewRecord($data) {
	self::$localrecords = $data;
    }

    public function onUpdateRecords($data) {
	self::$localrecords = $data;
    }

    public function onDedimaniaUpdateRecords($data) {
	
    }

    public function onDedimaniaNewRecord($data) {
	
    }

    public function onDedimaniaPlayerConnect($data) {
	/* if (count(self::$dedirecords) > 0) {
	  $this->needUpdate = self::Dedimania_force;
	  } */
    }

    public function onDedimaniaPlayerDisconnect() {
	
    }

    public function onDedimaniaRecord($record, $oldrecord) {
	
    }

}

?>