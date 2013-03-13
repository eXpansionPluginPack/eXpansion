<?php

namespace ManiaLivePlugins\eXpansion\RecordWidgets;

use \ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\Dedimania\Events\Event as DediEvent;
use ManiaLivePlugins\eXpansion\LocalRecords\Events\Event as LocalEvent;

class RecordWidgets extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin implements \ManiaLivePlugins\eXpansion\LocalRecords\Events\Listener, \ManiaLivePlugins\eXpansion\Dedimania\Events\Listener {

    public function exp_onInit() {
        $this->setVersion(0.1);
    }

    public function exp_onLoad() {
        Dispatcher::register(DediEvent::getClass(), $this, DediEvent::ON_GET_RECORDS);
        Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_UPDATE_RECORDS);
    }

    public function exp_onReady() {
        $this->enableDedicatedEvents();
        foreach ($this->storage->players as $player)
            $this->onPlayerConnect($player->login, false); // create panel for everybody
        foreach ($this->storage->spectators as $player)
            $this->onPlayerConnect($player->login, true); // create panel for everybody
    }

    public function onUpdateRecords($data) {
        Gui\Widgets\RecordsPanel::$localrecords = $data;
        Gui\Widgets\RecordsPanel::RedrawAll();
    }

    public function onDedimaniaGetRecords($data) {
        Gui\Widgets\RecordsPanel::$dedirecords = $data['Records'];
        Gui\Widgets\RecordsPanel::RedrawAll();
    }

    public function onPlayerConnect($login, $isSpectator) {
        $panel = Gui\Widgets\RecordsPanel::Create($login);
        $panel->setSize(50, 60);
        $panel->setPosition(-160, 60);
        $panel->show();
    }

    public function onPlayerDisconnect($login) {
        Gui\Widgets\RecordsPanel::Erase($login);
    }

    public function onDedimaniaOpenSession($sessionId) {
        
    }

    public function onNewRecord($data) {
        
    }

}

?>
