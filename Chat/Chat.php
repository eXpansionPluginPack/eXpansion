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
use ManiaLivePlugins\eXpansion\Chat\Gui\Widgets\ChatSelect;
use ManiaLivePlugins\eXpansion\Core\types\config\Variable;

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

    public static $channels = array();
    public static $playerChannels = array("Public");

    /** @var Config */
    private $config;
    private $exclude = array();
    private $badWords = array();

    public function eXpOnLoad()
    {
        $this->loadProfanityList();
        self::$channels = array_merge(array("Public"), Config::getInstance()->channels);
    }

    public function eXpOnReady()
    {
        $this->enableDedicatedEvents(Event::ON_PLAYER_CONNECT);
        $this->enableDedicatedEvents(Event::ON_PLAYER_DISCONNECT);

        Dispatcher::register(Event::getClass(), $this, Event::ON_PLAYER_CHAT, 10);

        try {
            $this->connection->chatEnableManualRouting(true);
            $cmd = AdminGroups::addAdminCommand('chat', $this, 'admChat', Permission::GAME_SETTINGS);
            $cmd->setHelp('/adm chat enable or disable');
            $this->registerChatCommand("chat", "cmdChat", 1, true);
            $this->registerChatCommand("chat", "cmdChat", 0, true);
        } catch (\Exception $e) {
            $this->console("Couldn't initialize chat. Error from server: " . $e->getMessage());
            $this->enabled = false;
        }

        if (Config::getInstance()->useChannels) {
            $this->initChat();
        }

        $this->config = Config::getInstance();
    }


    public function initChat()
    {
        $all = $this->storage->players + $this->storage->spectators;
        foreach ($all as $login => $player) {
            self::$playerChannels[$login] = "Public";
            $this->displayWidget($login);
        }
    }

    private function loadProfanityList()
    {
        $ignore = array(".", "..", "LICENSE", "README.md", "USERS.md", ".git");
        $path = realpath(APP_ROOT)
            . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "bad_words"
            . DIRECTORY_SEPARATOR . "List-of-Dirty-Naughty-Obscene-and-Otherwise-Bad-Words-master";

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

    public function onSettingsChanged(Variable $var)
    {

        if ($var->getConfigInstance() instanceof Config) {
            if ($var->getName() == "useChannels") {
                if ($var->getRawValue() == true) {
                    $this->initChat();
                } else {
                    ChatSelect::EraseAll();
                }
            }
            if ($var->getName() == "channels") {
                self::$channels = array_merge(array("Public"), $var->getRawValue());
                if (Config::getInstance()->useChannels) {
                    ChatSelect::EraseAll();
                    $this->initChat();
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

    public function cmdChat($login, $params = "help")
    {
        switch (strtolower($params)) {
            case "on":
                if (array_key_exists($login, $this->exclude)) {
                    unset($this->exclude[$login]);
                }
                $this->eXpChatSendServerMessage(eXpGetMessage("Chat messages enabled."), $login);
                break;
            case "off":
                $this->exclude[$login] = $login;
                $this->eXpChatSendServerMessage(eXpGetMessage("Chat messages disabled."), $login);
                break;
            default:
                $this->eXpChatSendServerMessage(eXpGetMessage("Usage: /chat on or /chat off."), $login);
                break;
        }
    }

    public function admChat($login, $params)
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
        self::$playerChannels[$login] = "Public";
        $this->displayWidget($login);
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
        if (isset(self::$playerChannels['login'])) {
            unset(self::$playerChannels['login']);
        }
        $player = $this->storage->getPlayerObject($login);
        if (empty($player)) {
            return;
        }
        \ManiaLive\Utilities\Logger::getLog('chat')->write(
            " (" . $player->iPAddress . ") [" . $login . "] Disconnected"
        );

        ChatSelect::Erase($login);
    }

    public function displayWidget($login)
    {
        $widget = ChatSelect::Create($login);
        $widget->sync();
        $widget->show();
    }

    public function getRecepients()
    {

        $array = array_values(
            $this->storage->spectators + AdminGroups::getAdminsByPermission(Permission::CHAT_ON_DISABLED)
        );
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
     * @param int $playerUid
     * @param string $login
     * @param string $text
     * @param bool $isRegistredCmd
     *
     * * @return void
     */
    public function onPlayerChat($playerUid, $login, $text, $isRegistredCmd)
    {

        if ($playerUid != 0 && substr($text, 0, 2) == " /") {
            Dispatcher::dispatch(new Event("PlayerChat", array($playerUid, $login, ltrim($text), true)));
            return;
        }

        if ($playerUid != 0 && substr($text, 0, 1) != "/" && $this->enabled) {
            $config = Config::getInstance();
            $force = "";
            $source_player = $this->storage->getPlayerObject($login);
            if ($config->allowMPcolors) {
                if (strstr($source_player->nickName, '$>')) {
                    $nick = $source_player->nickName;
                    $pos = strpos($source_player->nickName, '$>');
                    $color = substr($source_player->nickName, $pos);
                    if (substr($nick, -1) == '$') {
                        $nick = substr($nick, 0, -1);
                    }
                    if ($color != '$>$') {
                        $force = str_replace('$>', "", $color);
                    }
                }
            }
            if ($config->useProfanityFilter) {
                $text = $this->applyFilter($text);
            }

            if ($source_player == null) {
                return;
            }
            $nick = $source_player->nickName;
            $nick = str_ireplace('$w', '', $nick);
            $nick = str_ireplace('$z', '$z$s', $nick);
            // fix for chat...
            $nick = str_replace('$<', '', $nick);
            $text = str_replace('$<', '', $text);

            if ($this->config->publicChatActive || AdminGroups::hasPermission($login, Permission::CHAT_ON_DISABLED)) {
                $playersCombined = $this->storage->players + $this->storage->spectators;
                $channels = array();
                $currentChannel = self::$playerChannels[$login];

                foreach (self::$playerChannels as $key => $value) {
                    $channels[$value][] = $key;
                }

                // by default set global channel
                $receivers = null;
                $channel = "";

                if (Config::getInstance()->useChannels) {
                    // if group
                    if (self::$playerChannels[$login] != "Public") {
                        $channel = "[" . ucfirst($currentChannel) . "] ";
                        $receivers = implode(",", array_intersect(array_keys($playersCombined),
                                (AdminGroups::getAdminsByPermission(Permission::CHAT_ADMINCHAT)
                                    + $channels[$currentChannel]))
                        );
                    }
                }

                try {
                    // change text color, if admin is defined at admingroups
                    if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::isInList($login)) {
                        $color = $config->adminChatColor;

                        if ($this->expStorage->isRelay) {
                            $color = $config->otherServerChatColor;
                        }
                        $this->connection->chatSendServerMessage(
                            $channel .
                            $config->adminSign . '$fff$<' . $nick . '$z$s$> '
                            . $config->chatSeparator . $color . $force . $text,
                            $receivers
                        );
                    } else {
                        $color = $config->publicChatColor;
                        if ($this->expStorage->isRelay) {
                            $color = $config->otherServerChatColor;
                        }

                        $this->connection->chatSendServerMessage(
                            $channel . '$fff$<' . $nick . '$z$s$> ' . $config->chatSeparator . $color . $force . $text,
                            $receivers
                        );
                    }
                    $nickLog = \ManiaLib\Utils\Formatting::stripStyles($nick);

                    \ManiaLive\Utilities\Logger::getLog('chat')->write("[" . $login . "] " . $nickLog . " - " . $text);
                } catch (\Exception $e) {
                    $this->console(
                        __(
                            'error sending chat from %s: %s with folloing error %s',
                            $login,
                            $login,
                            $text,
                            $e->getMessage()
                        )
                    );
                }
            } else {
                // chat is disabled
                $recepient = $this->getRecepients();

                if ($config->enableSpectatorChat) {

                    if (in_array($login, $recepient)) {


                        $color = $config->otherServerChatColor;
                        $this->connection->chatSendServerMessage(
                            '$fff$<' . $nick . '$z$s$> ' . $config->chatSeparator . $color . $force . $text,
                            $recepient
                        );
                        $nickLog = \ManiaLib\Utils\Formatting::stripStyles($nick);
                        \ManiaLive\Utilities\Logger::getLog('chat')->write(
                            "[" . $login . "] " . $nickLog . " - " . $text
                        );
                    } else {
                        $this->eXpChatSendServerMessage(
                            "#error#Chat is disabled at at the moment!!! "
                            . "You can chat when you retire or go spectator. You may still use PM messages",
                            $login,
                            array()
                        );
                    }
                } else {
                    $this->eXpChatSendServerMessage(
                        "#error#Chat is disabled at at the moment!!! "
                        . "Only admins may chat. You may still use PM messages",
                        $login,
                        array()
                    );
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
    public function eXpOnUnload()
    {
        Dispatcher::unregister(Event::getClass(), $this, Event::ON_PLAYER_CHAT);
        $this->connection->chatEnableManualRouting(false);
        ChatSelect::EraseAll();
    }
}
