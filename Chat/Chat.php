<?php
/**
 * eXpansion - Chat plugin
 *
 * @name Chat
 * @date      29-01-2013
 * @version   r1
 * @package   eXpansion
 *
 * @author    Petri Järvisalo
 * @copyright 2013
 *
 */

namespace ManiaLivePlugins\eXpansion\Chat;

use ManiaLive\DedicatedApi\Callback\Event;
use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;

/**
 * Redirects the chat in order to display it nicer.
 * Can be used to disable the chat as well.
 *
 * @package ManiaLivePlugins\eXpansion\Chat
 *
 * @author  Reaby
 */
class Chat extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{
    /** Is the redirection enabled or not ?
     *
     * @type bool
     */
    private $enabled = true;

    /** @var Config */
    private $config;
    private $exclude = array();
    public $badWords = array();

    function eXpOnLoad()
    {
        $this->loadProfanityList();
    }

    function eXpOnReady()
    {
        $this->enableDedicatedEvents(Event::ON_PLAYER_CONNECT);
        $this->enableDedicatedEvents(Event::ON_PLAYER_DISCONNECT);

        Dispatcher::register(Event::getClass(), $this, Event::ON_PLAYER_CHAT, 10);

        try {
            $this->connection->chatEnableManualRouting(true);
            $cmd = AdminGroups::addAdminCommand('chat', $this, 'adm_chat', Permission::game_settings);
            $cmd->setHelp('/adm chat enable or disable');
            $this->registerChatCommand("chat", "cmd_chat", 1, true);
            $this->registerChatCommand("chat", "cmd_chat", 0, true);
        } catch (\Exception $e) {
            $this->console("[eXpansion|Chat] Couldn't initialize chat. Error from server: " . $e->getMessage());
            $this->enabled = false;
        }

        $this->config = Config::getInstance();
    }

    private function loadProfanityList()
    {
        $ignore = array(".", "..", "LICENSE", "README.md", "USERS.md", ".git");
        $path = realpath(APP_ROOT) . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "bad_words" . DIRECTORY_SEPARATOR . "List-of-Dirty-Naughty-Obscene-and-Otherwise-Bad-Words-master";

        if (is_dir($path)) {

            $this->console("[Chat] loading profanity filter words...");
            $dir = new \DirectoryIterator($path);
            foreach ($dir as $file) {
                if (!in_array($file->getBaseName(), $ignore)) {
                    foreach (file($file->getPathname()) as $line) {
                        $this->badWords[] = strtolower(trim($line, "\r\n"));
                    }
                }
            }
        }
    }

    public function applyFilter($text)
    {
        $out = array();
        $words = explode(" ", $text);
        foreach ($words as $word) {
            if (in_array(strtolower($word), $this->badWords)) {
                $out[] = str_repeat("#", strlen($word));
            } else {
                $out[] = $word;
            }
        }

        return implode(" ", $out);
    }

    public function cmd_chat($login, $params = "help")
    {
        switch (strtolower($params)) {
            case "on":
                if (array_key_exists($login, $this->exclude)) {
                    unset($this->exclude[$login]);
                }
                $this->eXpChatSendServerMessage(exp_getMessage("Chat messages enabled."), $login);
                break;
            case "off":
                $this->exclude[$login] = $login;
                $this->eXpChatSendServerMessage(exp_getMessage("Chat messages disabled."), $login);
                break;
            default:
                $this->eXpChatSendServerMessage(exp_getMessage("Usage: /chat on or /chat off."), $login);
                break;
        }
    }

    public function adm_chat($login, $params)
    {
        $command = array_shift($params);

        $var = MetaData::getInstance()->getVariable('publicChatActive');

        switch (strtolower($command)) {
            case "enable":
                $var->setRawValue(true);
                $this->eXpChatSendServerMessage("#admin_action#Public chat is now #variable#Enabled");
                break;
            case "disable":
                $var->setRawValue(false);
                $this->eXpChatSendServerMessage("#admin_action#Public chat is now #variable#Disabled");
                break;
        }
    }

    /**
     * On Player connect just show console
     *
     * @param $login
     * @param $isSpectator
     */
    public function onPlayerConnect($login, $isSpectator)
    {
        $player = $this->storage->getPlayerObject($login);
        $nickLog = \ManiaLib\Utils\Formatting::stripStyles($player->nickName);
        \ManiaLive\Utilities\Logger::getLog('chat')->write(
            " (" . $player->iPAddress . ") [" . $login . "] Connect with nickname " . $nickLog
        );
    }

    /**
     * On player just disconnect
     *
     * @param      $login
     * @param null $reason
     */
    public function onPlayerDisconnect($login, $reason = null)
    {
        $player = $this->storage->getPlayerObject($login);
        if (empty($player)) return;
        \ManiaLive\Utilities\Logger::getLog('chat')->write(
            " (" . $player->iPAddress . ") [" . $login . "] Disconnected"
        );
    }

    public function getRecepients()
    {

        $array = array_values($this->storage->spectators + AdminGroups::getAdminsByPermission(Permission::chat_onDisabled));
        foreach (\ManiaLivePlugins\eXpansion\Core\Core::$playerInfo as $login => $playerinfo) {
            if ($playerinfo->hasRetired) {
                $array[] = $playerinfo->login;
            }
        }

        $recepients = array();
        foreach ($array as $player) {
            if ($player instanceof \ManiaLive\Data\Player) {
                $recepients[$player->login] = $player->login;
            } else {
                $recepients[$player] = $player;
            }
        }

        foreach ($this->exclude as $login => $player) {
            if (array_key_exists($login, $recepients)) {
                unset($recepients[$login]);
            }
        }

        return array_keys(array_intersect_key(($this->storage->players + $this->storage->spectators), $recepients));
    }

    /**
     * onPlayerChat()
     * Processes the chat incoming from server, changes the look and color.
     *
     * @param int    $playerUid
     * @param string $login
     * @param string $text
     * @param bool   $isRegistredCmd
     *
     * * @return void
     */
    function onPlayerChat($playerUid, $login, $text, $isRegistredCmd)
    {
        if ($playerUid != 0 && substr($text, 0, 1) != "/" && $this->enabled) {
            $config = Config::getInstance();
            $force = "";
            if ($config->allowMPcolors) {
                if (strstr($source_player->nickName, '$>')) {

                    $pos = strpos($source_player->nickName, '$>');
                    $color = substr($source_player->nickName, $pos);
                    if (substr($nick, -1) == '$') $nick = substr($nick, 0, -1);
                    if ($color != '$>$') $force = str_replace('$>', "", $color);
                }
            }
            if ($config->useProfanityFilter) $text = $this->applyFilter($text);

            $source_player = $this->storage->getPlayerObject($login);
            if ($source_player == null) return;
            $nick = $source_player->nickName;
            $nick = str_ireplace('$w', '', $nick);
            $nick = str_ireplace('$z', '$z$s', $nick);
            // fix for chat...
            $nick = str_replace('$<', '', $nick);
            $text = str_replace('$<', '', $text);

            if ($this->config->publicChatActive || AdminGroups::hasPermission($login, Permission::chat_onDisabled)) {

                try {
                    // change text color, if admin is defined at admingroups
                    if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::isInList($login)) {
                        $color = $config->adminChatColor;

                        if ($this->expStorage->isRelay) {
                            $color = $config->otherServerChatColor;
                        }
                        $this->connection->chatSendServerMessage(
                            $config->adminSign . '$fff$<' . $nick . '$z$s$> ' . $config->chatSeparator . $color . $force . $text
                        );
                    } else {
                        $color = $config->publicChatColor;
                        if ($this->expStorage->isRelay) {
                            $color = $config->otherServerChatColor;
                        }

                        $this->connection->chatSendServerMessage('$fff$<' . $nick . '$z$s$> ' . $config->chatSeparator . $color . $force . $text);
                    }
                    $nickLog = \ManiaLib\Utils\Formatting::stripStyles($nick);

                    \ManiaLive\Utilities\Logger::getLog('chat')->write("[" . $login . "] " . $nickLog . " - " . $text);
                } catch (\Exception $e) {
                    $this->console(
                        __('[eXpansion|Chat] error sending chat from %s: %s with folloing error %s', $login, $login, $text, $e->getMessage())
                    );
                }
            } else {
                // chat is disabled
                $recepient = $this->getRecepients();

                if ($config->enableSpectatorChat) {

                    if (in_array($login, $recepient)) {


                        $color = $config->otherServerChatColor;
                        $this->connection->chatSendServerMessage('$fff$<' . $nick . '$z$s$> ' . $config->chatSeparator . $color . $force . $text,
                            $recepient);
                        $nickLog = \ManiaLib\Utils\Formatting::stripStyles($nick);
                        \ManiaLive\Utilities\Logger::getLog('chat')->write("[" . $login . "] " . $nickLog . " - " . $text);
                    } else {
                        $this->eXpChatSendServerMessage("#error#Chat is disabled at at the moment!!! You can chat when you retire or go spectator. You may still use PM messages",
                            $login, array());
                    }
                } else {
                    $this->eXpChatSendServerMessage("#error#Chat is disabled at at the moment!!! Only admins may chat. You may still use PM messages",
                        $login, array());
                }
            }
        }
    }

    /**
     * onUnload()
     * Function called on unloading this plugin.
     *
     * @return void
     */
    function eXpOnUnload()
    {
        Dispatcher::unregister(Event::getClass(), $this, Event::ON_PLAYER_CHAT);
        $this->connection->chatEnableManualRouting(false);
    }
}

?>