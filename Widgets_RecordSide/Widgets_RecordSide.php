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
	$this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_ROUNDS);
	$this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK);
	$this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM);
	$this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS);
	$this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP);
	$this->addDependency(new \ManiaLive\PluginHandler\Dependency('\ManiaLivePlugins\\eXpansion\\LocalRecords\\LocalRecords'));
    }

    public function exp_onLoad() {
	if ($this->isPluginLoaded('\ManiaLivePlugins\\eXpansion\\Dedimania\\Dedimania'))
	    Dispatcher::register(\ManiaLivePlugins\eXpansion\Dedimania\Events\Event::getClass(), $this);

	Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_RECORDS_LOADED);
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
	// $this->forceUpdate = true;

	$this->getRoundsPoints();
	$this->updateDediPanel();
	$this->updateLocalPanel();
	$this->updateLivePanel();
    }

    public function onTick() {

	if ((time() - $this->lastUpdate) > 1 && $this->needUpdate !== false || $this->forceUpdate == true) {

	    if (($this->needUpdate & self::Dedimania) == self::Dedimania || $this->forceUpdate || ($this->needUpdate & self::Dedimania_force) == self::Dedimania_force) {
		if ($this->dedi || $this->needUpdate == self::Dedimania_force) {
		    $this->updateDediPanel();
		    $this->dedi = false;
		}
	    }

	    if (($this->needUpdate & self::Localrecords) == self::Localrecords || $this->forceUpdate) {
		if ($this->local) {
		    $this->updateLocalPanel();
		    $this->local = false;
		}
	    }

	    $this->lastUpdate = time();
	    $this->forceUpdate = false;
	    $this->needUpdate = false;
	}
    }

    public function updateDediPanel($login = NULL) {

	$panelMain = Gui\Widgets\DediPanel::Create($login);
	$panelMain->setPosition(-160, 63);
	$panelMain->setSize(40, 95);
	$panelMain->setNbFields(20);
	$panelMain->setNbFirstFields(5);
	$panelMain->setLayer(\ManiaLive\Gui\Window::LAYER_NORMAL);
	if($login == Null){
	    $this->widgetIds["DediPanel"] = $panelMain->getId();
	}else if(isset($this->widgetIds["DediPanel"])){
	    $panelMain->setId($this->widgetIds["DediPanel"]);
	}
	
	$panelScore = Gui\Widgets\DediPanel2::Create($login);
	$panelScore->setPosition(-160, 63);
	$panelScore->setSize(40, 95);
	$panelScore->setNbFields(25);
	$panelScore->setNbFirstFields(8);
	$panelScore->setLayer(\ManiaLive\Gui\Window::LAYER_SCORES_TABLE);
	if($login == Null){
	    $this->widgetIds["DediPanel"] = $panelScore->getId();
	}else if(isset($this->widgetIds["DediPanel"])){
	    $panelScore->setId($this->widgetIds["DediPanel"]);
	}
	
	if($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_ROUNDS
		|| $this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_TEAM
		|| $this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_LAPS
		|| $this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_CUP){
	    
	    $panelMain->setPosition(-161, 63);		
	    $panelMain->setNbFields(12);
	    $panelMain->setNbFirstFields(5);
	    
	    $panelScore->setPosition(-161, 63);	
	    $panelScore->setNbFields(12);
	    $panelScore->setNbFirstFields(5);
	}
	
	$panelScore->update();
	$panelMain->update();
	$panelMain->show();
	$panelScore->show();
    }

    public function updateLocalPanel($login = null) {
	$panelMain = Gui\Widgets\LocalPanel::Create($login);
	$panelMain->setPosition(118, 52);	
	$panelMain->setSize(40, 95);
	$panelMain->setNbFields(15);
	$panelMain->setNbFirstFields(5);
	$panelMain->setLayer(\ManiaLive\Gui\Window::LAYER_NORMAL);
	if($login == Null){
	    $this->widgetIds["LocalPanel"] = $panelMain->getId();
	}else if(isset($this->widgetIds["LocalPanel"])){
	    $panelMain->setId($this->widgetIds["LocalPanel"]);
	}
	

	$panelScore = Gui\Widgets\LocalPanel2::Create($login);
	$panelScore->setPosition(118, 52);
	$panelScore->setSize(40, 95);
	$panelScore->setNbFields(15);
	$panelScore->setNbFirstFields(5);
	$panelScore->setLayer(\ManiaLive\Gui\Window::LAYER_SCORES_TABLE);
	if($login == Null){
	    $this->widgetIds["LocalPanel2"] = $panelScore->getId();
	}else if(isset($this->widgetIds["LocalPanel2"])){
	    $panelScore->setId($this->widgetIds["LocalPanel2"]);
	}
	
	if($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_ROUNDS
		|| $this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_TEAM
		|| $this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_LAPS
		|| $this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_CUP){
	    
	    $panelMain->setPosition(-161, 9);	
	    $panelMain->setNbFields(12);
	    $panelMain->setNbFirstFields(3);
	    
	    $panelScore->setPosition(-161, 9);	
	    $panelScore->setNbFields(12);
	    $panelScore->setNbFirstFields(3);
	}
	
	$panelScore->update();
	$panelMain->update();
	$panelMain->show();
	$panelScore->show();
    }

    public function updateLivePanel($login = null) {
	Gui\Widgets\LivePanel::$connection = $this->connection;
	
	$panelMain = Gui\Widgets\LivePanel::Create($login);
	$panelMain->setPosition(118, -12);
	$panelMain->setSize(40, 95);
	$panelMain->setNbFields(8);
	$panelMain->setNbFirstFields(3);	
	$panelMain->setLayer(\ManiaLive\Gui\Window::LAYER_NORMAL);
	if($login == Null){
	    $this->widgetIds["LivePanel"] = $panelMain->getId();
	}else if(isset($this->widgetIds["LivePanel"])){
	    $panelMain->setId($this->widgetIds["LivePanel"]);
	}
	

	$panelScore = Gui\Widgets\LivePanel2::Create($login);
	$panelScore->setPosition(118, -12);
	$panelScore->setSize(40, 95);
	$panelScore->setNbFields(8);
	$panelScore->setNbFirstFields(3);
	$panelScore->setLayer(\ManiaLive\Gui\Window::LAYER_SCORES_TABLE);
	if($login == Null){
	    $this->widgetIds["LivePanel2"] = $panelScore->getId();
	}else if(isset($this->widgetIds["LivePanel2"])){
	    $panelScore->setId($this->widgetIds["LivePanel2"]);
	}
	
	if($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_ROUNDS
		|| $this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_TEAM
		|| $this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_CUP){
	    \ManiaLive\Gui\CustomUI::HideForAll(\ManiaLive\Gui\CustomUI::ROUND_SCORES);
	}
	
	if($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_ROUNDS
		|| $this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_TEAM
		|| $this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_LAPS
		|| $this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_CUP){
	    
	    $panelMain->setPosition(118, 42);	
	    $panelMain->setNbFields(22);
	    $panelMain->setNbFirstFields(15);
	    
	    $panelScore->setPosition(118, 42);	
	    $panelScore->setNbFields(22);
	    $panelScore->setNbFirstFields(15);
	}
	
	$panelScore->update();
	$panelMain->update();
	$panelMain->show();
	$panelScore->show();
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
	self::$dedirecords = array(); // reset 
	self::$localrecords = array(); //  reset
	Gui\Widgets\LocalPanel::EraseAll();
	Gui\Widgets\LocalPanel2::EraseAll();
	Gui\Widgets\DediPanel::EraseAll();
	Gui\Widgets\DediPanel2::EraseAll();
	$this->hideLivePanel();
    }

    public function getRoundsPoints(){
	$points = $this->connection->getRoundCustomPoints();
	if(empty($points)){
	    $maxPoints = 10;

	    self::$roundPoints = array();
	    for($i = $maxPoints; $i > 0; $i--){
		self::$roundPoints[] = $i;
	    }
	}else {
	    self::$roundPoints = $points;
	}
    }
    
    public function onBeginMatch() {
	$this->getRoundsPoints();	
	
	self::$raceOn = false;
	$this->dedi = true;
	$this->local = true;
	$this->forceUpdate = true;
	$this->updateDediPanel();
	$this->updateLocalPanel();
	$this->updateLivePanel();
	self::$secondMap = true;
	self::$raceOn = true;
    }
    
    public function onEndRound() {
	//@TOdo remove it is good to have it to keep track of other players
	//if($this->storage->gameInfos->gameMode != GameInfos::GAMEMODE_ROUNDS){
	    //$this->hideLivePanel();
	//}
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

    public function onUpdateRecords($data) {
	
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
    }

    public function onDedimaniaOpenSession() {
	
    }

    public function onNewRecord($data) {
	
    }

    public function onDedimaniaUpdateRecords($data) {
	
    }

    public function onDedimaniaNewRecord($data) {
	
    }

    public function onDedimaniaPlayerConnect($data) {
	if (count(self::$dedirecords) > 0) {
	    $this->needUpdate = self::Dedimania_force;
	}
    }

    public function onDedimaniaPlayerDisconnect() {
	
    }

    public function onDedimaniaRecord($record, $oldrecord) {
	
    }

}

?>