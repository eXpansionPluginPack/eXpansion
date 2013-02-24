<?php

/**
 * eXpansion - Chat plugin
 *
 * @name Chat
 * @date 29-01-2013
 * @version r1
 * @package eXpansion
 *
 * @author Petri Järvisalo
 * @copyright 2013
 *
 */ 

namespace ManiaLivePlugins\eXpansion\Chat;

use ManiaLive\Utilities\Console;
use ManiaLive\Features\Admin\AdminGroup;
use ManiaLivePlugins\eXpansion\Chat\Config;

class Chat extends \ManiaLive\PluginHandler\Plugin {
    /** Is the redirection enabled or not ?
     * @type bool */
    private $enabled = true;

    /**
     * onInit()
     *
     * @return void
     */
    function onInit() {
        $this->setVersion("0.1");
    }

    /**
     * onLoad()
     * Function called on loading of ManiaLive.
     *
     * @return void
     */
    function onLoad() {
        $this->enableDedicatedEvents();
        try {
            $this->connection->chatEnableManualRouting(true);
        } catch (\Exception $e) {
            Console::println('[eXpansion|Chat] Couldn\'t initialize chat.' . "\n" . ' Error from server: ' . $e->getMessage());
            $this->enabled = false;
        }
    }
    
    /**
     * onPlayerChat()
     * Processes the chat incoming from server, changes the look and color.
     *
     *  @param int $playerUid
     *  @param string $login 
     *  @param string $text
     *  @param bool $isRegistredCmd 
     *  
     * * @return void
     */
    function onPlayerChat($playerUid, $login, $text, $isRegistredCmd) {
        if ($playerUid != 0 && substr($text, 0, 1) != "/" && $this->enabled) {
            $config = Config::getInstance();
            $source_player = $this->storage->getPlayerObject($login);
            $nick = $source_player->nickName;
            $nick = str_ireplace('$w', '', $nick);

            try {
                if (AdminGroup::contains($login)) {
                    $this->connection->chatSendServerMessage("\$fff" . $config->adminSign . " $nick\$z\$s" . $config->adminChatColor . "  " . $text);
                } elseif ($source_player->isManagedByAnOtherServer) {
                    $this->connection->chatSendServerMessage("\$fff$nick\$z\$s" . $config->otherServerChatColor . "  " . $text);
                } else {
                    $this->connection->chatSendServerMessage("\$fff$nick\$z\$s" . $config->publicChatColor . "  " . $text);
                }
            } catch (\Exception $e) {
                Console::println('[eXpansion|Chat] error sending chat from ' . $login . ': ' . $text . ' with folloing error' . "\n" . $e->getMessage());
            }
        }
    }

    /**
     * onUnload()
     * Function called on unloading this plugin.
     *
     * @return void
     */
    function onUnload() {
        $this->connection->chatEnableManualRouting(false);
        parent::onUnload();
    }

}

?>