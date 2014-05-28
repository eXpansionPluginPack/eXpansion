<?php

namespace ManiaLivePlugins\eXpansion\Widgets_DedimaniaRecords;

use ManiaLive\Event\Dispatcher;

class Widgets_DedimaniaRecords extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    const None = 0x0;
    const Dedimania = 0x2;
    const Dedimania_force = 0x8;
    const All = 0x31;

    public static $me = null;
    public static $dedirecords = array();
    public static $secondMap = false;
    private $lastUpdate;
    private $forceUpdate = false;
    private $needUpdate = false;
    private $dedi = true;
    private $widgetIds = array();
    public static $raceOn;
    public static $roundPoints;

    /** @var Config */
    private $config;

    public function exp_onLoad() {
	if ($this->isPluginLoaded('\ManiaLivePlugins\\eXpansion\\Dedimania\\Dedimania') || $this->isPluginLoaded('\ManiaLivePlugins\\eXpansion\\Dedimania_Script\\Dedimania_Script'))
	    Dispatcher::register(\ManiaLivePlugins\eXpansion\Dedimania\Events\Event::getClass(), $this);

	$this->config = Config::getInstance();
    }

    public function exp_onReady() {
	$this->enableDedicatedEvents();

	$this->lastUpdate = time();
	$this->enableTickerEvent();

	$this->updateDediPanel();
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

	$dedi1 = '\ManiaLivePlugins\\eXpansion\\Dedimania\\Dedimania';
	$dedi2 = '\ManiaLivePlugins\\eXpansion\\Dedimania_Script\\Dedimania_Script';

	try {
	    if (($this->isPluginLoaded($dedi1) && $this->callPublicMethod($dedi1, 'isRunning')) || ($this->isPluginLoaded($dedi2) && $this->callPublicMethod($dedi2, 'isRunning'))
	    ) {
		if ($login == Null) {
		    //Gui\Widgets\DediPanel::EraseAll();
		    $panelMain = Gui\Widgets\DediPanel::Create($login);
		    $panelMain->setLayer(\ManiaLive\Gui\Window::LAYER_NORMAL);
		    $panelMain->setSizeX(40);
		    $this->widgetIds["DediPanel"] = $panelMain;
		    $this->widgetIds["DediPanel"]->update();
		    $this->widgetIds["DediPanel"]->show();
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
		    $this->widgetIds["DediPanel2"]->update();
		    $this->widgetIds["DediPanel2"]->show();
		} else if (isset($this->widgetIds["DediPanel2"])) {
		    $this->widgetIds["DediPanel2"]->update();
		    $this->widgetIds["DediPanel2"]->show($login);
		}
	    }
	} catch (\Exception $ex) {
	    
	}
    }

    public function showDediPanel($login) {
	$this->updateDediPanel($login);
    }


    public function onEndMatch($rankings, $winnerTeamOrMap) {

	self::$raceOn = false;
	$this->widgetIds = array();
	Gui\Widgets\DediPanel::EraseAll();
	Gui\Widgets\DediPanel2::EraseAll();
    }

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {
	if ($wasWarmUp) {
	    self::$raceOn = false;
	    $this->forceUpdate = true;
	    $this->updateDediPanel();
	    self::$secondMap = true;
	    self::$raceOn = true;
	} else {
	    self::$dedirecords = array(); // reset
	    $this->widgetIds = array();
	    Gui\Widgets\DediPanel::EraseAll();
	    Gui\Widgets\DediPanel2::EraseAll();
	}
    }

    public function onBeginMap($map, $warmUp, $matchContinuation) {
	self::$raceOn = false;
	$this->forceUpdate = true;
	$this->widgetIds = array();
	Gui\Widgets\DediPanel::EraseAll();
	Gui\Widgets\DediPanel2::EraseAll();
	self::$secondMap = true;
	self::$raceOn = true;
    }

    public function onBeginMatch() {
	self::$raceOn = false;
	$this->forceUpdate = true;
	$this->widgetIds = array();
	Gui\Widgets\DediPanel::EraseAll();
	Gui\Widgets\DediPanel2::EraseAll();
	self::$secondMap = true;
	self::$raceOn = true;
    }

    public function onEndRound() {
	//@TOdo remove it is good to have it to keep track of other players
	/* if($this->storage->gameInfos->gameMode != GameInfos::GAMEMODE_ROUNDS){
	  $this->hideLivePanel();
	  } */
    }

    public function onDedimaniaGetRecords($data) {
	self::$dedirecords = $data['Records'];
	$this->dedi = True;
	$this->needUpdate = self::Dedimania_force;
    }

    public function onPlayerConnect($login, $isSpectator) {
	$this->showDediPanel($login);
    }

    public function onPlayerDisconnect($login, $reason = null) {
	Gui\Widgets\DediPanel::Erase($login);
	Gui\Widgets\DediPanel2::Erase($login);
    }

    public function onDedimaniaOpenSession() {
	
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

    function exp_onUnload() {
	Gui\Widgets\DediPanel::EraseAll();
	Gui\Widgets\DediPanel2::EraseAll();
    }

}

?>