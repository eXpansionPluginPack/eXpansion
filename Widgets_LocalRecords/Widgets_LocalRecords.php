<?php

namespace ManiaLivePlugins\eXpansion\Widgets_LocalRecords;

use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\LocalRecords\Events\Event as LocalEvent;
use Maniaplanet\DedicatedServer\Structures\GameInfos;

class Widgets_LocalRecords extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    public static $me = null;
    public static $localrecords = array();
    public static $secondMap = false;

    private $widgetIds = array();
    public static $raceOn;
    public static $roundPoints;

    /** @var Config */
    private $config;

    public function exp_onLoad() {

	Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_RECORDS_LOADED);
	Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_NEW_RECORD);
	Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_UPDATE_RECORDS);
	$this->config = Config::getInstance();
    }

    public function exp_onReady() {
	$this->enableDedicatedEvents();

	$this->lastUpdate = time();
	if ($this->isPluginLoaded('\ManiaLivePlugins\eXpansion\\LocalRecords\\LocalRecords'))
	    self::$localrecords = $this->callPublicMethod("\\ManiaLivePlugins\\eXpansion\\LocalRecords\\LocalRecords", "getRecords");

	$this->updateLocalPanel();
	self::$me = $this;
    }



    public function updateLocalPanel($login = null) {

	if ($this->isPluginLoaded('\ManiaLivePlugins\eXpansion\\LocalRecords\\LocalRecords')) {
	    if ($login == Null) {
		//Gui\Widgets\LocalPanel::EraseAll();
		$panelMain = Gui\Widgets\LocalPanel::Create($login);
		$panelMain->setSizeX(40);
		$panelMain->setLayer(\ManiaLive\Gui\Window::LAYER_NORMAL);
		$this->widgetIds["LocalPanel"] = $panelMain;
		$this->widgetIds["LocalPanel"]->update();
		$this->widgetIds["LocalPanel"]->show();
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
		$this->widgetIds["LocalPanel2"]->update();
		$this->widgetIds["LocalPanel2"]->show();
	    } else if (isset($this->widgetIds["LocalPanel2"])) {
		$this->widgetIds["LocalPanel2"]->update();
		$this->widgetIds["LocalPanel2"]->show($login);
	    }
	}
    }

    public function showLocalPanel($login) {
	$this->updateLocalPanel($login);
    }



    public function onEndMatch($rankings, $winnerTeamOrMap) {

	self::$raceOn = false;
	$this->widgetIds = array();
	Gui\Widgets\LocalPanel::EraseAll();
	Gui\Widgets\LocalPanel2::EraseAll();
    }

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {
	if ($wasWarmUp) {
	    self::$raceOn = false;
	    $this->forceUpdate = true;
	    $this->updateLocalPanel();
	    self::$secondMap = true;
	    self::$raceOn = true;
	} else {
	    self::$localrecords = array(); //  reset
	    $this->widgetIds = array();
	    Gui\Widgets\LocalPanel::EraseAll();
	    Gui\Widgets\LocalPanel2::EraseAll();
	}
    }

    public function onBeginMap($map, $warmUp, $matchContinuation) {
	self::$raceOn = false;
	$this->forceUpdate = true;
	$this->widgetIds = array();
	Gui\Widgets\LocalPanel::EraseAll();
	Gui\Widgets\LocalPanel2::EraseAll();
	$this->updateLocalPanel();
	self::$secondMap = true;
	self::$raceOn = true;
    }

    public function onBeginMatch() {
	self::$raceOn = false;
	$this->forceUpdate = true;
	$this->widgetIds = array();
	Gui\Widgets\LocalPanel::EraseAll();
	Gui\Widgets\LocalPanel2::EraseAll();
	$this->updateLocalPanel();
	self::$secondMap = true;
	self::$raceOn = true;
    }

    public function onEndRound() {
	//@TOdo remove it is good to have it to keep track of other players
	/* if($this->storage->gameInfos->gameMode != GameInfos::GAMEMODE_ROUNDS){
	  $this->hideLivePanel();
	  } */
    }

    public function onRecordsLoaded($data) {
	self::$localrecords = $data;
	$this->local = true;
	$this->needUpdate = self::$localrecords;
    }



    public function onPlayerConnect($login, $isSpectator) {
	$this->showLocalPanel($login);
    }

    public function onPlayerDisconnect($login, $reason = null) {
	Gui\Widgets\LocalPanel::Erase($login);
	Gui\Widgets\LocalPanel2::Erase($login);
    }


    public function onNewRecord($data) {
	self::$localrecords = $data;
    }

    public function onUpdateRecords($data) {
	self::$localrecords = $data;
    }


    function exp_onUnload() {
	Gui\Widgets\LocalPanel::EraseAll();
	Gui\Widgets\LocalPanel2::EraseAll();
    }

}

?>