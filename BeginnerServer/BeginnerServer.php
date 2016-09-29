<?php

namespace ManiaLivePlugins\eXpansion\BeginnerServer;

class BeginnerServer extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    public $msg_notBeginner;
    public $msg_isBeginner;

    public function eXpOnLoad()
    {
        $this->msg_notBeginner = eXpGetMessage(
            "This is beginner friendly server, "
            . "since you have more than 100k ladder rank, you are automatically forced to spectate!"
        );
        $this->msg_isBeginner = eXpGetMessage(
            "Welcome to play at beginner friendly server, "
            ."players more than 100k ladder rank are automatically forced to spectate :)"
        );
    }

    public function eXpOnReady()
    {
        $this->enableDedicatedEvents();
        $this->connection->setServerTag("server.isBeginner", "true");
        $this->connection->getServerTags();
    }

    public function onPlayerConnect($login, $isSpectator)
    {
        $this->checkBeginner($login);
    }

    public function checkBeginner($login)
    {
        $player = $this->storage->getPlayerObject($login);
        if (!$player->spectator) {
            if ($player->ladderRanking < 100000) {
                $this->eXpChatSendServerMessage($this->msg_notBeginner, $login);
                $this->connection->forceSpectator($login, 1);
            } else {
                $this->eXpChatSendServerMessage($this->msg_isBeginner, $login);
            }
        }
    }

    public function onPlayerInfoChanged($playerInfo)
    {
        $player = \ManiaLive\Data\Player::fromArray($playerInfo);

        if (!$player->spectator) {
            $this->checkBeginner($player->login);
        }
    }
}
