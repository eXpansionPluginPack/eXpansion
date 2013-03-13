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
      //  $this->registerChatCommand("check", "checkSession", 0, true);
        $this->dedimania->openSession();
    }

    function checkSession($login) {
        $this->dedimania->checkSession();
    }

    public function onPlayerConnect($login, $isSpectator) {
        $player = $this->storage->getPlayerObject($login);
        $this->dedimania->playerConnect($player, $isSpectator);
    }
    
    public function onPlayerDisconnect($login) {
        $this->dedimania->playerDisconnect($login);
    }
    
    public function onBeginMap($map, $warmUp, $matchContinuation) {        
        $this->getRecords();
    }

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {    
        $this->dedimania->updateServerPlayers($map);
    }

    public function onDedimaniaOpenSession($sessionId) {
        $this->dedimania->getChallengeRecords();        
    }

    public function onDedimaniaGetRecords($data) {
      
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
