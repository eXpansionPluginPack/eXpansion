<?php

namespace ManiaLivePlugins\eXpansion\Widgets_RecordSide;

use \ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\LocalRecords\Events\Event as LocalEvent;

class Widgets_RecordSide extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    const Dedimania = 2;
    const Localrecords = 4;
    const All = 31;

    public static $dedirecords = array();
    public static $localrecords = array();
    private $lastUpdate;
    private $forceUpdate = false;
    private $needUpdate = false;

    /** @var Config */
    private $config;

    public function exp_onInit() {
        $this->addDependency(new \ManiaLive\PluginHandler\Dependency('ManiaLivePlugins\\eXpansion\\LocalRecords\\LocalRecords'));
    }

    public function exp_onLoad() {
        if ($this->isPluginLoaded('ManiaLivePlugins\\eXpansion\\Dedimania\\Dedimania'))
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
        Gui\Widgets\LocalPanel::EraseAll();
        Gui\Widgets\DediPanel::EraseAll();
    }

    public function onTick() {
        
        if ((time() - $this->lastUpdate) > 5 && $this->needUpdate !== false || $this->forceUpdate == true) {
            
             if ( ($this->needUpdate & self::Dedimania) == self::Dedimania || $this->forceUpdate) {
                foreach (Gui\Widgets\DediPanel::GetAll() as $panel) {
                    try {
                        $panel->update();
                    } catch (\Exception $e) {
                        $this->console("update failed." . $e->getMessage());
                    }
                }
                Gui\Widgets\DediPanel::RedrawAll();
            }         
            
            if ( ($this->needUpdate & self::Localrecords) == self::Localrecords || $this->forceUpdate) {
                echo "update!";
                foreach (Gui\Widgets\LocalPanel::GetAll() as $panel) {
                    try {
                        $panel->update();
                    } catch (\Exception $e) {
                        $this->console("update failed." . $e->getMessage());
                    }
                }
                Gui\Widgets\LocalPanel::RedrawAll();
            }

            $this->lastUpdate = time();
            $this->forceUpdate = false;
            $this->needUpdate = false;
        }
    }

    public function onBeginMatch() {
        self::$localrecords = $this->callPublicMethod("ManiaLivePlugins\\eXpansion\\LocalRecords\\LocalRecords", "getRecords");
        foreach ($this->storage->players as $player)
            $this->onPlayerConnect($player->login, false); // create panel for everybody
        foreach ($this->storage->spectators as $player)
            $this->onPlayerConnect($player->login, true); // create panel for everybody
        $this->forceUpdate = true;
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
        $panel = Gui\Widgets\LocalPanel::Create($login);
        $panel->setPosition(118, 50);
        $panel->setSize(40, 95);
        $panel->update();
        $panel->show();

        $panel = Gui\Widgets\DediPanel::Create($login);
        $panel->setPosition(-160, 60);
        $panel->setSize(40, 95);
        $panel->update();
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
