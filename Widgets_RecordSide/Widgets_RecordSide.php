<?php

namespace ManiaLivePlugins\eXpansion\Widgets_RecordSide;

use \ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\LocalRecords\Events\Event as LocalEvent;

class Widgets_RecordSide extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

	const None = 0x0;
	const Dedimania = 0x2;
	const Localrecords = 0x4;
	const All = 0x31;

	public static $dedirecords = array();
	public static $localrecords = array();
	private $lastUpdate;
	private $forceUpdate = false;
	private $needUpdate = false;
	private $dedi = true;
	private $local = true;

	/** @var Config */
	private $config;

	public function exp_onInit() {
		$this->addDependency(new \ManiaLive\PluginHandler\Dependency('ManiaLivePlugins\\eXpansion\\LocalRecords\\LocalRecords'));
	}

	public function exp_onLoad() {
		if ($this->isPluginLoaded('ManiaLivePlugins\\eXpansion\\Dedimania\\Dedimania'))
			Dispatcher::register(\ManiaLivePlugins\eXpansion\Dedimania\Events\Event::getClass(),
					$this);

		Dispatcher::register(LocalEvent::getClass(), $this,
				LocalEvent::ON_UPDATE_RECORDS);
		$this->config = Config::getInstance();
	}

	public function exp_onReady() {
		$this->enableDedicatedEvents();
		foreach ($this->storage->players as $player)
			$this->onPlayerConnect($player->login, false); // create panel for everybody
		foreach ($this->storage->spectators as $player)
			$this->onPlayerConnect($player->login, true); // create panel for everybody

		$this->lastUpdate = time();
		self::$localrecords = $this->callPublicMethod("ManiaLivePlugins\\eXpansion\\LocalRecords\\LocalRecords",
				"getRecords");
		$this->enableTickerEvent();
		$this->needUpdate = self::Localrecords;
		// $this->forceUpdate = true;
	}

	public function onEndMatch($rankings, $winnerTeamOrMap) {
		self::$dedirecords = array(); // reset 
		self::$localrecords = array(); //  reset
		Gui\Widgets\LocalPanel::EraseAll();
		Gui\Widgets\DediPanel::EraseAll();
	}

	public function onTick() {

		if ((time() - $this->lastUpdate) > 1 && $this->needUpdate !== false || $this->forceUpdate == true) {

			if (($this->needUpdate & self::Dedimania) == self::Dedimania || $this->forceUpdate) {
				if ($this->dedi) {
					foreach (Gui\Widgets\DediPanel::GetAll() as $panel) {
						try {
							$panel->update();
						}
						catch (\Exception $e) {
							$this->console("update failed." . $e->getMessage());
						}
					}
					Gui\Widgets\DediPanel::RedrawAll();
					$this->dedi = false;
				}
			}

			if (($this->needUpdate & self::Localrecords) == self::Localrecords || $this->forceUpdate) {
				if ($this->local) {
					foreach (Gui\Widgets\LocalPanel::GetAll() as $panel) {
						try {
							$panel->update();
						}
						catch (\Exception $e) {
							$this->console("update failed." . $e->getMessage());
						}
					}
					Gui\Widgets\LocalPanel::RedrawAll();
					$this->local = false;
				}
			}

			$this->lastUpdate = time();
			$this->forceUpdate = false;
			$this->needUpdate = false;
		}
	}

	public function onBeginMatch() {
		$this->dedi = true;
		$this->local = true;
		self::$localrecords = $this->callPublicMethod("ManiaLivePlugins\\eXpansion\\LocalRecords\\LocalRecords",
				"getRecords");
		foreach ($this->storage->players as $player)
			$this->onPlayerConnect($player->login, false); // create panel for everybody
		foreach ($this->storage->spectators as $player)
			$this->onPlayerConnect($player->login, true); // create panel for everybody
			
// $this->forceUpdate = true;
	}

	public function onUpdateRecords($data) {
		self::$localrecords = $data;
		$this->needUpdate = self::Localrecords;
	}

	public function onDedimaniaUpdateRecords($data) {
		self::$dedirecords = $data['Records'];
		$this->needUpdate = self::Dedimania;
	}

	public function onDedimaniaGetRecords($data) {
		self::$dedirecords = $data['Records'];
		$this->needUpdate = self::Dedimania;
	}

	public function onPlayerConnect($login, $isSpectator) {

		$panel = Gui\Widgets\LocalPanel::Create($login, false);
		$panel->update();
		$panel->setPosition(118, 50);
		$panel->setSize(40, 95);
		$panel->setNbFields(20);
		$panel->setNbFirstFields(5);
		$panel->setLayer(\ManiaLive\Gui\Window::LAYER_NORMAL);
		$panel->show();

		$panel = Gui\Widgets\LocalPanel::Create($login, false);
		$panel->update();
		$panel->setPosition(118, 50);
		$panel->setSize(40, 95);
		$panel->setNbFields(20);
		$panel->setNbFirstFields(5);
		$panel->setLayer(\ManiaLive\Gui\Window::LAYER_SCORES_TABLE);
		$panel->show();

		$panel = Gui\Widgets\DediPanel::Create($login, false);
		$panel->update();
		$panel->setPosition(-160, 60);
		$panel->setSize(40, 95);
        $panel->setNbFields(20);
		$panel->setNbFirstFields(5);
		$panel->setLayer(\ManiaLive\Gui\Window::LAYER_NORMAL);
		$panel->show();

		$panel = Gui\Widgets\DediPanel::Create($login, false);
		$panel->update();
		$panel->setPosition(-160, 60);
		$panel->setSize(40, 95);
        $panel->setNbFields(20);
		$panel->setNbFirstFields(5);
		$panel->setLayer(\ManiaLive\Gui\Window::LAYER_SCORES_TABLE);
		$panel->show();
	}

	public function onPlayerDisconnect($login, $reason = null) {
		Gui\Widgets\LocalPanel::Erase($login);
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
