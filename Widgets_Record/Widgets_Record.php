<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Record;

use \ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\LocalRecords\Events\Event as LocalEvent;

class Widgets_Record extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    public static $dedirecords = array();
    public static $localrecords = array();
    private $lastUpdate;
    private $forceUpdate = false;
    private $needUpdate = false;

    /** @var Config */
    private $config;

    public function exp_onInit() {
	$this->addDependency(new \ManiaLive\PluginHandler\Dependency('ManiaLivePlugins\eXpansion\LocalRecords\LocalRecords'));
    }

    public function exp_onLoad() {
	if ($this->isPluginLoaded('eXpansion\Dedimania'))
	    Dispatcher::register(\ManiaLivePlugins\eXpansion\Dedimania\Events\Event::getClass(), $this);

	Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_UPDATE_RECORDS);
	$this->config = Config::getInstance();
    }

    public function exp_onReady() {
	$this->enableDedicatedEvents();
	foreach ($this->storage->players as $player)
	    $this->onPlayerConnect($player->login, false); // create panel for everybody
	foreach ($this->storage->spectators as $player)
	    $this->onPlayerConnect($player->login, true); // create panel for everybody

	$this->lastUpdate = time();
	self::$localrecords = $this->callPublicMethod("ManiaLivePlugins\\eXpansion\\LocalRecords\\LocalRecords", "getRecords");
	$this->enableTickerEvent();
	$this->forceUpdate = true;
    }

    public function onEndMatch($rankings, $winnerTeamOrMap) {
	self::$dedirecords = array(); // reset 
	self::$localrecords = array(); //  reset
	switch ($this->config->layer) {
	    case "score":
		Gui\Widgets\RecordsPanelTab::EraseAll();
		break;

	    default:
		Gui\Widgets\RecordsPanel::EraseAll();
		break;
	}
    }

    public function onTick() {
	if ((time() - $this->lastUpdate) > 5 && $this->needUpdate || $this->forceUpdate == true) {
	    $this->lastUpdate = time();
	    $this->forceUpdate = false;
	    $this->needUpdate = false;
	    switch ($this->config->layer) {
		case "score":
		    foreach (Gui\Widgets\RecordsPanelTab::GetAll() as $panel) {
			try {
			    $panel->update();
			} catch (\Exception $e) {
			    // silent exception
			}
		    }
		    Gui\Widgets\RecordsPanelTab::RedrawAll();
		    break;
		default:
		    foreach (Gui\Widgets\RecordsPanel::GetAll() as $panel) {
			try {
			    $panel->update();
			} catch (\Exception $e) {
			    // silent exception
			}
		    }
		    Gui\Widgets\RecordsPanel::RedrawAll();
		    break;
	    }
	}
    }

    public function onBeginMatch() {
	self::$localrecords = $this->callPublicMethod("eXpansion\\LocalRecords", "getRecords");
	foreach ($this->storage->players as $player)
	    $this->onPlayerConnect($player->login, false); // create panel for everybody
	foreach ($this->storage->spectators as $player)
	    $this->onPlayerConnect($player->login, true); // create panel for everybody
	$this->forceUpdate = true;
    }

    public function onUpdateRecords($data) {
	self::$localrecords = $data;
	$this->needUpdate = true;
    }

    public function onDedimaniaUpdateRecords($data) {
	self::$dedirecords = $data['Records'];
	$this->needUpdate = true;
    }

    public function onDedimaniaGetRecords($data) {
	self::$dedirecords = $data['Records'];
	$this->needUpdate = true;
	$this->debug("[Localrecords widget]: Found " . sizeof($data['Records']) . " dedimania records for current map!\n");
    }

    public function onPlayerConnect($login, $isSpectator) {
	switch ($this->config->layer) {
	    case "score":
		$panel = Gui\Widgets\RecordsPanelTab::Create($login);
		$panel->setLayer(\ManiaLive\Gui\Window::LAYER_SCORES_TABLE);
		$panel->update();
		$panel->show();
		break;

	    default:
		$panel = Gui\Widgets\RecordsPanel::Create($login);
		$panel->setLayer(\ManiaLive\Gui\Window::LAYER_NORMAL);
		$panel->setScreenEdge("left");
		$panel->update();
		$panel->show();
		break;
	}
    }

    public function onPlayerDisconnect($login, $reason = null) {
	Gui\Widgets\RecordsPanel::Erase($login);
	Gui\Widgets\RecordsPanelTab::Erase($login);
    }

    public function onDedimaniaOpenSession() {
	
    }

    public function onNewRecord($data) {
	
    }

    public function onDedimaniaNewRecord($data) {
	
    }

    public function onDedimaniaPlayerConnect($data) {
	
    }

    public function onDedimaniaPlayerDisconnect() {
	
    }

    public function onDedimaniaRecord($record, $oldrecord) {
	
    }

}

?>
