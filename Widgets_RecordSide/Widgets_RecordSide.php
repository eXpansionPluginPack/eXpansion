<?php

namespace ManiaLivePlugins\eXpansion\Widgets_RecordSide;

use \ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\LocalRecords\Events\Event as LocalEvent;

class Widgets_RecordSide extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    const None = 0x0;
    const Dedimania = 0x2;
    const Localrecords = 0x4;
    const Dedimania_force = 0x8;
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
        $this->needUpdate = self::Localrecords;
        // $this->forceUpdate = true;
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

    public function onEndMatch($rankings, $winnerTeamOrMap) {
        self::$dedirecords = array(); // reset 
        self::$localrecords = array(); //  reset
        Gui\Widgets\LocalPanel::EraseAll();
        Gui\Widgets\DediPanel::EraseAll();
        $this->hideLivePanel();
    }
    
    public function onBeginMatch() {
        $this->dedi = true;
        $this->local = true;
        $this->forceUpdate = true;
        $this->updateDediPanel();
        $this->updateLocalPanel();
        $this->updateLivePanel();
    }

    public function onUpdateRecords($data) {
        self::$localrecords = $data;
        $this->needUpdate = self::Localrecords;
    }

    public function onDedimaniaUpdateRecords($data) {
        self::$dedirecords = $data['Records'];
        $this->dedi = True;
        $this->needUpdate = self::Dedimania_force;
    }

    public function onDedimaniaGetRecords($data) {
        self::$dedirecords = $data['Records'];
        $this->dedi = True;
        $this->needUpdate = self::Dedimania_force;
    }

    public function updateDediPanel() {
        foreach ($this->storage->players as $player)
            $this->showDediPanel($player->login); // create panel for everybody
        foreach ($this->storage->spectators as $player)
            $this->showDediPanel($player->login); // create panel for everybody
    }

    public function updateLocalPanel() {
        foreach ($this->storage->players as $player)
            $this->showLocalPanel($player->login); // create panel for everybody
        foreach ($this->storage->spectators as $player)
            $this->showLocalPanel($player->login); // create panel for everybody
    }
    
    public function updateLivePanel(){
         foreach ($this->storage->players as $player)
            $this->showLivePanel($player->login); // create panel for everybody
        foreach ($this->storage->spectators as $player)
            $this->showLivePanel($player->login); // create panel for everybody
    }

    public function showLocalPanel($login) {
        //Gui\Widgets\LocalPanel::Erase($login);
        //Gui\Widgets\LocalPanel2::Erase($login);

        $panel = Gui\Widgets\LocalPanel::Create($login);
        $panel->setPosition(118, 53);
        $panel->setSize(40, 95);
        $panel->setNbFields(15);
        $panel->setNbFirstFields(5);
        $panel->update();
        $panel->setLayer(\ManiaLive\Gui\Window::LAYER_NORMAL);
        $panel->show();

        $panel = Gui\Widgets\LocalPanel2::Create($login);
        $panel->setPosition(118, 50);
        $panel->setSize(40, 95);
        $panel->setNbFields(20);
        $panel->setNbFirstFields(5);
        $panel->update();
        $panel->setLayer(\ManiaLive\Gui\Window::LAYER_SCORES_TABLE);
        $panel->show();
    }

    public function showDediPanel($login) {
        //Gui\Widgets\DediPanel::Erase($login);
        //Gui\Widgets\DediPanel2::Erase($login);

        $panel = Gui\Widgets\DediPanel::Create($login);
        $panel->setPosition(-160, 60);
        $panel->setSize(40, 95);
        $panel->setNbFields(20);
        $panel->setNbFirstFields(5);
        $panel->update();
        $panel->setLayer(\ManiaLive\Gui\Window::LAYER_NORMAL);
        $panel->show();

        $panel = Gui\Widgets\DediPanel2::Create($login);
        $panel->setPosition(-160, 60);
        $panel->setSize(40, 95);
        $panel->setNbFields(20);
        $panel->setNbFirstFields(5);
        $panel->update();
        $panel->setLayer(\ManiaLive\Gui\Window::LAYER_SCORES_TABLE);
        $panel->show();
    }
    
    public function showLivePanel($login){
        //if($this->storage->serverStatus->code == \Maniaplanet\DedicatedServer\Structures\Status::PLAY){
            $panel = Gui\Widgets\LivePanel::Create($login);
            $panel->setPosition(118, -12);
            $panel->setSize(40, 95);
            $panel->setNbFields(8);
            $panel->setNbFirstFields(3);
            $panel->update();
            $panel->setLayer(\ManiaLive\Gui\Window::LAYER_NORMAL);
            $panel->show();
        //}
    }
    
    public function hideLivePanel(){
        echo "Destroy ALLLLLL";
        Gui\Widgets\LivePanel::EraseAll();
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