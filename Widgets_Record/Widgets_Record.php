<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Record;

use \ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\Dedimania\Events\Event as DediEvent;
use ManiaLivePlugins\eXpansion\LocalRecords\Events\Event as LocalEvent;

class Widgets_Record extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin implements \ManiaLivePlugins\eXpansion\LocalRecords\Events\Listener, \ManiaLivePlugins\eXpansion\Dedimania\Events\Listener {

    private $lastUpdate;
    private $forceUpdate = false;
    private $needUpdate = false;

    public function exp_onInit() {
        $this->setVersion(0.1);
    }

    public function exp_onLoad() {
        Dispatcher::register(DediEvent::getClass(), $this);
        Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_UPDATE_RECORDS);
    }

    public function exp_onReady() {
        $this->enableDedicatedEvents();
        foreach ($this->storage->players as $player)
            $this->onPlayerConnect($player->login, false); // create panel for everybody
        foreach ($this->storage->spectators as $player)
            $this->onPlayerConnect($player->login, true); // create panel for everybody 

        $this->lastUpdate = time();
        $this->enableTickerEvent();
    }

    public function onTick() {
        if ((time() - $this->lastUpdate) > 5 && $this->needUpdate || $this->forceUpdate == true) {
            $this->lastUpdate = time();
            $this->forceUpdate = false;
            $this->needUpdate = false;

            foreach (Gui\Widgets\RecordsPanel::GetAll() as $panel) {
                try {
                    $panel->update();
                    $panel->redraw($panel->getRecipient());
                } catch (\Exception $e) {
                  // silent exception  
                }
            }
        }
    }

    public function onUpdateRecords($data) {
        Gui\Widgets\RecordsPanel::$localrecords = $data;
        $this->needUpdate = true;
    }

    public function onDedimaniaUpdateRecords($data) {
        Gui\Widgets\RecordsPanel::$dedirecords = $data['Records'];
        $this->needUpdate = true;
    }

    public function onDedimaniaGetRecords($data) {
        Gui\Widgets\RecordsPanel::$dedirecords = $data['Records'];
        $this->needUpdate = true;
        $this->exp_chatSendServerMessage("Found %s Dedimania records for current map.", null, array(sizeof($data['Records'])));
        echo "Dedimania: Found " . sizeof($data['Records']) . " records for current map!";
    }

    public function onPlayerConnect($login, $isSpectator) {
        $panel = Gui\Widgets\RecordsPanel::Create($login);
        $panel->setSize(50, 60);
        $panel->setPosition(-160, 60);
        $panel->update();
        $panel->show();
    }

    public function onPlayerDisconnect($login) {
        Gui\Widgets\RecordsPanel::Erase($login);
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

}

?>
