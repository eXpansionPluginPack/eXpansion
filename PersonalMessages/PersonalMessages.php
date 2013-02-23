<?php

namespace ManiaLivePlugins\eXpansion\PersonalMessages;

class PersonalMessages extends  \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    public static $reply = array();

    public function exp_onReady() {
        $this->enableDedicatedEvents();
        $this->registerChatCommand("pmx", "sendPersonalMessage", -1, true);
        $this->registerChatCommand("r", "sendReply", -1, true);
    }

    public function onPlayerDisconnect($login) {
        \ManiaLivePlugins\eXpansion\PersonalMessages\Gui\Windows\PmWindow::Erase($login);
        if (isset(self::$reply[$login]))
            unset(self::$reply[$login]);
    }

    public function sendPersonalMessage($login, $message = "") {
        $window = \ManiaLivePlugins\eXpansion\PersonalMessages\Gui\Windows\PmWindow::Create($login);
        $window->setTitle('Select Player to send message');
        $window->setMessage($message);
        $window->setSize(120, 100);
        $window->centerOnScreen();
        $window->show();
    }

    public function sendReply($login, $message) {
        try {
            if (isset(self::$reply[$login])) {
                $targetPlayer = $this->storage->getPlayerObject(self::$reply[$login]);
                $sourcePlayer = $this->storage->getPlayerObject($login);
                $this->connection->chatSendServerMessage('$abcYou whisper to ' . ($targetPlayer->nickName) . '$z$s$abc: ' . $message, $login);
                $this->connection->chatSendServerMessage('$abcA whisper from ' . ($sourcePlayer->nickName) . '$z$s$abc: ' . $message, self::$reply[$login]);
            } else {
                $this->connection->chatSendServerMessage('$abcNo one to whisper back', $login);
            }
        } catch (\Exception $e) {
            \ManiaLive\Utilities\Console::println("Error sending a reply" .$e->getMessage());
        }
    }

}
?>
