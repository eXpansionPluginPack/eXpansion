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

class Chat extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    /** Is the redirection enabled or not ?
     * @type bool */
    private $enabled = true;

    /** @var Config */
    private $config;

    /**
     * onLoad()
     * Function called on loading of ManiaLive.
     *
     * @return void
     */
    function exp_onLoad() {
        $this->enableDedicatedEvents();
        try {
            $this->connection->chatEnableManualRouting(true);
        } catch (\Exception $e) {
            $this->console(__("[eXpansion|Chat] Couldn't initialize chat. Error from server: %s", $e->getMessage()));
            $this->enabled = false;
        }
        $this->config = Config::getInstance();
    }

    public function onPlayerConnect($login, $isSpectator) {
        $player = $this->storage->getPlayerObject($login);
        $nickLog = \ManiaLib\Utils\Formatting::stripStyles($player->nickName);
        \ManiaLive\Utilities\Logger::getLog('chat')->write(" (" . $player->iPAddress . ") [" . $login . "] Connect with nickname " . $nickLog);
    }

    public function onPlayerDisconnect($login, $reason = null) {
        $player = $this->storage->getPlayerObject($login);
        if (empty($player))
            return;
        \ManiaLive\Utilities\Logger::getLog('chat')->write(" (" . $player->iPAddress . ") [" . $login . "] Disconnected");
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
            $config = $this->config;
            $source_player = $this->storage->getPlayerObject($login);
            $nick = $source_player->nickName;
            $nick = str_ireplace('$w', '', $nick);
            $nick = str_ireplace('$z', '$z$s', $nick);
            /*
              $smileys = array("ッ", "ツ", "シ");
              $rnd = rand(0, sizeof($smileys) - 1);
              $text = str_replace(array(":)", "=)"), $smileys[$rnd], $text);
             */

            $force = "";
            if ($config->allowMPcolors) {
                if (strstr($source_player->nickName, '$>')) {

                    $pos = strpos($source_player->nickName, '$>');
                    $color = substr($source_player->nickName, $pos);
                    if (substr($nick, -1) == '$')
                        $nick = substr($nick, 0, -1);
                    if ($color != '$>$')
                        $force = str_replace('$>', "", $color);
                }
            }

            try {
                if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, "server_admin")) {
                    $this->connection->chatSendServerMessage("\$fff" . $config->adminSign . " $nick\$z\$s " . $config->chatSeparator . $config->adminChatColor . $force . $text);
                } else {
                    $color = $config->publicChatColor;
                    if ($source_player->isManagedByAnOtherServer) {
                        $color = $config->otherServerChatColor;
                    }
                    $this->connection->chatSendServerMessage("\$fff$nick\$z\$s " . $config->chatSeparator . $color . $force . $text);
                }
                $nickLog = \ManiaLib\Utils\Formatting::stripStyles($nick);

                \ManiaLive\Utilities\Logger::getLog('chat')->write("[" . $login . "] " . $nickLog . " - " . $text);
            } catch (\Exception $e) {
                $this->console(__('[eXpansion|Chat] error sending chat from %s: %s with folloing error %s', $login, $login, $text, $e->getMessage()));
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