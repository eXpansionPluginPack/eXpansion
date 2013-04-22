<?php

namespace ManiaLivePlugins\eXpansion\PersonalMessages;

class PersonalMessages extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    public static $reply = array();
    private $message ;
    public function exp_onReady() {
        $this->enableDedicatedEvents();
        $this->registerChatCommand("pmx", "sendPersonalMessage", -1, true);
        $this->registerChatCommand("r", "sendReply", -1, true);

        foreach ($this->storage->players as $player)
            $this->onPlayerConnect($player->login, false);
        foreach ($this->storage->spectators as $player)
            $this->onPlayerConnect($player->login, true);
    }

    function onPlayerConnect($login, $isSpectator) {
        $info = Gui\Widgets\MessagesPanel::Create($login);
        $info->setSize(100, 20);
        $info->setPosition(-160, -58);
        $info->show();
    }

    public function onPlayerDisconnect($login, $reason = null) {
        \ManiaLivePlugins\eXpansion\PersonalMessages\Gui\Windows\PmWindow::Erase($login);
        if (isset(self::$reply[$login]))
            unset(self::$reply[$login]);
    }

    public function sendPersonalMessage($login, $message = "") {
        $window = \ManiaLivePlugins\eXpansion\PersonalMessages\Gui\Windows\PmWindow::Create($login);
        $window->setController($this);
        $window->setTitle(__('Select Player to send message'));
        $window->setMessage($message);
        $this->message = $message;
        $window->setSize(120, 100);
        $window->centerOnScreen();
        $window->show();
    }

    function sendPm($login, $target) {
        try {          
            \ManiaLivePlugins\eXpansion\PersonalMessages\Gui\Windows\PmWindow::Erase($login);
            $targetPlayer = $this->storage->getPlayerObject($target);
            $sourcePlayer = $this->storage->getPlayerObject($login);
            self::$reply[$login] = $target;            
            
            $this->connection->chatSendServerMessage('$abcYou whisper to ' . ($targetPlayer->nickName) . '$z$s$abc: ' . $this->message, $login);
            $this->connection->chatSendServerMessage('$abcA whisper from ' . ($sourcePlayer->nickName) . '$z$s$abc: ' . $this->message, $target);
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage('$f00$oError $z$s$fff' . $e->getMessage());
        }
    }

    public function sendReply($login, $message) {
        try {
            if (!isset($message)) {
                    $this->connection->chatSendServerMessage('$abcNo message to send to', $login);
            }
            if (isset(self::$reply[$login])) {
                $targetPlayer = $this->storage->getPlayerObject(self::$reply[$login]);
                $sourcePlayer = $this->storage->getPlayerObject($login);
                $this->connection->chatSendServerMessage('$abcYou whisper to ' . ($targetPlayer->nickName) . '$z$s$abc: ' . $message, $login);
                $this->connection->chatSendServerMessage('$abcA whisper from ' . ($sourcePlayer->nickName) . '$z$s$abc: ' . $message, self::$reply[$login]);
            } else {
                $this->connection->chatSendServerMessage('$abcNo one to whisper back', $login);
            }
        } catch (\Exception $e) {
            \ManiaLive\Utilities\Console::println("Error sending a reply" . $e->getMessage());
        }
    }

}

?>
