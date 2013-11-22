<?php

namespace ManiaLivePlugins\eXpansion\Chatlog;

/**
 * Description of Chatlog
 *
 * @author Reaby
 */
class Chatlog extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $log = array();
    private $historyLength = 100;

    public function exp_onLoad() {
        $this->enableDedicatedEvents(\ManiaLive\DedicatedApi\Callback\Event::ON_PLAYER_CHAT);
        $this->registerChatCommand("chatlog", "showLog", 0, true);
    }

    public function onPlayerChat($playerUid, $login, $text, $isRegistredCmd) {
        if ($playerUid == 0)
            return;
        if (trim($text[0]) == "/")
            return;
        $player = $this->storage->getPlayerObject($login);
        $chatMessage = new Structures\ChatMessage(time(), $login, $player->nickName, $text);
        array_unshift($this->log, $chatMessage);
        $this->log = array_slice($this->log, 0, $this->historyLength, True);
    }

    public function showLog($login) {
        $window = Gui\Windows\ChatlogWindow::Create($login);
        $window->setTitle(__('Chatlog', $login));
        $window->centerOnScreen();

        $window->populateList(array_reverse($this->log));
        $window->setSize(140, 100);
        $window->show();
    }

}

?>
