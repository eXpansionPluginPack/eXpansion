<?php

namespace ManiaLivePlugins\eXpansion\Emotes;

use \ManiaLivePlugins\eXpansion\Emotes\Gui\Windows\EmotePanel;

class Emotes extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $timeStamps = array();

    function exp_onReady() {
        $this->enableDedicatedEvents();
        EmotePanel::$emotePlugin = $this;

        $this->registerChatCommand("gg", "GG", 0, true);
        $this->registerChatCommand("bg", "BG", 0, true);
        $this->registerChatCommand("lol", "Lol", 0, true);
        $this->registerChatCommand("afk", "Afk", 0, true);
        $this->registerChatCommand("bootme", "BootMe", 0, true);

        foreach ($this->storage->players as $player)
            $this->onPlayerConnect($player->login, false);
        foreach ($this->storage->spectators as $player)
            $this->onPlayerConnect($player->login, true);
    }

    function onPlayerConnect($login, $isSpectator) {
        $info = EmotePanel::Create($login);
        $info->setSize(60, 20);
        $info->setPosition(-160, -52);
        $info->show();
    }

    public function onPlayerDisconnect($login) {
        EmotePanel::Erase($login);
        if (isset($this->timeStamps[$login]))
            unset($this->timeStamps[$login]);
    }

    public function GG($login) {
        $this->sendEmote($login, __FUNCTION__);
    }

    public function BG($login) {
        $this->sendEmote($login, __FUNCTION__);
    }

    public function Lol($login) {
        $this->sendEmote($login, __FUNCTION__);
    }

    public function Afk($login) {
        $this->sendEmote($login, __FUNCTION__);
    }

    public function BootMe($login) {
        $this->sendEmote($login, __FUNCTION__);
        $this->connection->kick($login);
    }

    public function sendEmote($login, $action) {
        try {
            if (!isset($this->timeStamps[$login])) {
                $this->timeStamps[$login] = time();
            } else {
                if (time() - $this->timeStamps[$login] < 2) {
                    return;
                }
            }

            $player = $this->storage->getPlayerObject($login);
            switch ($action) {
                case "GG":
                    $this->connection->chatSendServerMessage($player->nickName . '$z$s$i$o$f90 Good Game, everybody!');
                    break;
                case "BootMe":
                    $this->connection->chatSendServerMessage($player->nickName . '$z$s$i$o$f90 Chooses the real life! Cya..');
                    break;
                case "BG":
                    $this->connection->chatSendServerMessage($player->nickName . '$z$s$i$o$f90 I had a bad game :(');
                    break;
                case "Afk":
                    $this->connection->chatSendServerMessage($player->nickName . '$z$s$i$o$f90 is away from the keyboard!');
                    break;
                case "Lol":
                    $this->connection->chatSendServerMessage($player->nickName . '  $z$s$i$fff is laughing out loud: $o$FF0L$FE1o$FD1o$FB2o$FA2o$F93o$F93o$F72o$F52o$F41o$F21o$F00L');
                    break;
            }
            $this->timeStamps[$login] = time();
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(__('$f00$bError! $z$s$fff %s', $e->getMessage()), $login);
        }
    }

}

?>