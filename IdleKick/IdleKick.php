<?php

namespace ManiaLivePlugins\eXpansion\IdleKick;

use ManiaLivePlugins\eXpansion\IdleKick\Config;

class IdleKick extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $timeStamps = array();

    /** @var Config */
    private $config;

    function exp_onReady() {
        $this->enableDedicatedEvents();
        $this->enableTickerEvent();
        foreach ($this->storage->players as $player)
            $this->onPlayerConnect($player->login, false);
        foreach ($this->storage->spectators as $player)
            $this->onPlayerConnect($player->login, true);
        $this->config = Config::getInstance();
    }

    function onPlayerConnect($login, $isSpectator) {
        $this->checkActivity($login);
    }

    public function onPlayerDisconnect($login, $reason = null) {
        if (isset($this->timeStamps[$login]))
            unset($this->timeStamps[$login]);
    }

    public function onPlayerCheckpoint($playerUid, $login, $timeOrScore, $curLap, $checkpointIndex) {
        $this->checkActivity($login);
    }

    function onTick() {
        foreach ($this->timeStamps as $playerLogin => $value) {

            if ((time() - $value) > ($this->config->idleMinutes * 60)) {
                $player = $this->storage->getPlayerObject($playerLogin);
                $this->exp_chatSendServerMessage('IdleKick: %s', null, array($player->nickName));
                $this->connection->kick($playerLogin, "IdleKick");
                unset($this->timeStamps[$playerLogin]);
            }
        }
    }

    function checkActivity($login) {
        $this->timeStamps[$login] = time();
    }

    function onPlayerChat($playerUid, $login, $text, $isRegistredCmd) {
        $this->checkActivity($login);
    }

}

?>