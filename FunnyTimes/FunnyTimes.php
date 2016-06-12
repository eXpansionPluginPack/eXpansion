<?php

namespace ManiaLivePlugins\eXpansion\FunnyTimes;

use ManiaLive\Utilities\Time;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;

class FunnyTimes extends ExpPlugin
{

    private $msg_precise;
    private $msg_devilish;
    private $msg_funny;

    public function eXpOnReady()
    {
        $this->enableDedicatedEvents();
        $this->msg_precise = eXpGetMessage('$o$18fWhat a precise time! $z$fff %s $z$o$18f($z$fff%s$o$18f)');
        $this->msg_funny = eXpGetMessage('$o$6f0What a funny time!$z$fff %s $z$o$6f0($z$fff%s$o$6f0) ');
        $this->msg_devilish = eXpGetMessage('$o$f03Eek! What a devilish time!$z$fff %s $z$o$f03($z$fff%s$o$f03) \,,/');

    }


    public function onPlayerFinish($playerUid, $login, $timeOrScore)
    {
        $time = substr(strval($timeOrScore), -3);

        if ($timeOrScore == 0 || $time % 111 != 0) {
            return;
        }

        $message = $this->msg_funny;
        switch ($time) {
            case "000":
                $message = $this->msg_precise;
                break;
            case "666":
                $message = $this->msg_devilish;
                break;
        }

        $this->eXpChatSendServerMessage($message, null, array($this->storage->getPlayerObject($login)->nickName, Time::fromTM($timeOrScore)));
    }


}