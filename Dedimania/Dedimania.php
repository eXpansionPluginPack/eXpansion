<?php

namespace ManiaLivePlugins\eXpansion\Dedimania;

use ManiaLivePlugins\eXpansion\Dedimania\Classes\Connection as DediConnection;
use ManiaLivePlugins\eXpansion\Dedimania\Structures\Request;
use \ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\Dedimania\Events\Event;

class Dedimania extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin implements \ManiaLive\Event\Listener {

    /** @var DediConnection */
    public $dedimania;

    public function exp_onInit() {
        $this->setVersion(0.1);
        Dispatcher::register(Event::getClass(), $this);
    }

    public function exp_onLoad() {
        $this->enableDedicatedEvents();
        $this->enableApplicationEvents();
        $this->dedimania = DediConnection::getInstance();
    }

    public function exp_onReady() {
        $this->registerChatCommand("check", "checkSession", 0, true);
        $this->dedimania->openSession();
    }

    function checkSession($login) {
        $this->dedimania->checkSession();
    }

    public function onBeginMap($map, $warmUp, $matchContinuation) {
        print "onBeginMap";
        $this->getRecords();
    }

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {
        $this->dedimania->updateServerPlayers($map);
    }

    public function onDedimaniaOpenSession($sessionId) {
        foreach ($this->storage->players as $player) {
            if ($player->login != $this->storage->serverLogin)
                $this->dedimania->playerConnect($player, false);
        }
        foreach ($this->storage->spectators as $player)
            $this->dedimania->playerConnect($player, true);

        //$this->getRecords();
    }

    public function onDedimaniaGetRecords($data) {
        print_r($data);
    }

    public function getRecords() {
        $this->dedimania->getChallengeRecords();
    }

    public function onUnload() {
        $this->disableTickerEvent();
        $this->disableDedicatedEvents();
        parent::onUnload();
    }

}

?>
