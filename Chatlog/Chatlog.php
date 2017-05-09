<?php
namespace ManiaLivePlugins\eXpansion\Chatlog;

use ManiaLive\DedicatedApi\Callback\Event;
use ManiaLivePlugins\eXpansion\Chatlog\Gui\Windows\ChatlogWindow;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;

/**
 * Get all chat and logs it
 *
 * @author Reaby
 */
class Chatlog extends ExpPlugin
{

    private $log = array();

    public function eXpOnLoad()
    {
        $this->enableDedicatedEvents(Event::ON_PLAYER_CHAT);
        $this->registerChatCommand("chatlog", "showLog", 0, true);
        $this->setPublicMethod('showLog');
    }

    /**
     * When player chat we log it
     *
     * @param $playerUid
     * @param $login
     * @param $text
     * @param $isRegistredCmd
     */
    public function onPlayerChat($playerUid, $login, $text, $isRegistredCmd)
    {
        if ($playerUid == 0 || substr($text, 0, 1) == "/") {
            return;
        }
        $player = $this->storage->getPlayerObject($login);
        if ($player == null) {
            return;
        }
        $chatMessage = new Structures\ChatMessage(time(), $login, $player->nickName, $text);
        array_unshift($this->log, $chatMessage);
        $this->log = array_slice($this->log, 0, Config::getInstance()->historyLenght, true);
    }

    /**
     * Displays the chat log to the players
     *
     * @param $login
     */
    public function showLog($login)
    {
        /** @var ChatlogWindow $window */
        $window = ChatlogWindow::Create($login);
        $window->setTitle(__('Chatlog', $login));

        $window->setSize(140, 100);
        $window->populateList(array_reverse($this->log));
        $window->centerOnScreen();
        $window->show();
    }

    public function eXpOnUnload()
    {
        parent::eXpOnUnload();
        Gui\Windows\ChatlogWindow::EraseAll();
    }
}
