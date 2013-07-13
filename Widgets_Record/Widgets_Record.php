<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Record;

use \ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\LocalRecords\Events\Event as LocalEvent;

class Widgets_Record extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $lastUpdate;
    private $forceUpdate = false;
    private $needUpdate = false;

    public function exp_onInit() {
        parent::exp_onInit();
    }

    public function exp_onLoad() {
        if ($this->isPluginLoaded('Reaby\Dedimania'))
            Dispatcher::register(\ManiaLivePlugins\Reaby\Dedimania\Events\Event::getClass(), $this);
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
        $this->forceUpdate = true;
    }

    public function onEndMatch($rankings, $winnerTeamOrMap) {
        foreach (Gui\Widgets\RecordsPanel::GetAll() as $panel) {
            try {
                $panel->hide();
            } catch (\Exception $e) {
                // silent exception
            }
        }
    }

    public function onTick() {
        if ((time() - $this->lastUpdate) > 5 && $this->needUpdate || $this->forceUpdate == true) {
            $this->lastUpdate = time();
            $this->forceUpdate = false;
            $this->needUpdate = false;

            foreach (Gui\Widgets\RecordsPanel::GetAll() as $panel) {
                try {
                    $panel->update();
                } catch (\Exception $e) {
                    // silent exception
                }
            }
            Gui\Widgets\RecordsPanel::RedrawAll();
        }
    }

    public function onBeginMap($map, $warmUp, $matchContinuation) {
        Gui\Widgets\RecordsPanel::$dedirecords = array(); // on new map, reset deditimes from widget...
    }

    
    public function onBeginMatch() {
        foreach (Gui\Widgets\RecordsPanel::GetAll() as $panel) {
            try {
                $panel->show($panel->getRecipient());
            } catch (\Exception $e) {
                // silent exception
            }
        }
        
        $this->forceUpdate = true;
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
        echo "Dedimania: Found " . sizeof($data['Records']) . " records for current map!\n";
    }

    public function onPlayerConnect($login, $isSpectator) {
        $panel = Gui\Widgets\RecordsPanel::Create($login);
        $panel->setSize(38, 95);
        $panel->setPosition(-160, 60);
        $panel->update();
        $panel->show();
    }

    public function onPlayerDisconnect($login, $reason = null) {
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

    public function onDedimaniaRecord($record, $oldrecord) {
        
    }

}

?>
