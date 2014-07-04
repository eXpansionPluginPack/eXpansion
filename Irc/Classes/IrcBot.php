<?php

/*
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace ManiaLivePlugins\eXpansion\Irc\Classes;

define("BR", "\r\n");

class IrcException extends \Exception
{

}

/**
 * Description of IrcBot
 *
 * @author Petri
 */
class IrcBot
{

    const onConnect = "irc_onConnect";
    const onPublicChat = "irc_onPublicChat";
    const onPrivateMessage = "irc_onPrivateMessage";
    const onDisconnect = "irc_onDisconnect";

    /** @var \ResourceBundle|null */
    private $socket;
    private $callbackClasses = Array();

    /** @var IrcConfig */
    private $config;

    /** @var boolean */
    private $connected = false;
    private $isJoinedToChannel = false;

    final public function __construct(IrcConfig $config)
    {
        $this->config = $config;
        $this->socket = \socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($this->socket === false || !is_resource($this->socket))
            $this->throwError();

// @\socket_set_nonblock($this->socket);

        $this->connected = socket_connect($this->socket, $config->server, $config->port);
        if ($this->connected === false)
            $this->throwError();

        if (!empty($this->config->serverPass)) {
            $this->sendCommand("PASS", $this->config->serverPass);
        }
        $this->sendCommand("NICK", $this->config->nickname);
        $this->sendCommand("USER", $this->config->nickname . " 8 * :" . $this->config->realname);
    }

    /**
     * this should be called on main loop to work right
     * @return boolean
     */
    final public function onTick()
    {

        if ($this->connected === false)
            return false;

        if (is_resource($this->socket) === false)
            return false;


        $read = array($this->socket);
        $write = NULL;
        $except = NULL;
        $diff = socket_select($read, $write, $except, 0);

        if ($diff === false) {
            $this->throwError();
        }
        if ($diff > 0) {
            foreach ($read as $socket) {
                \socket_clear_error();
                $this->processRead($socket);
            }
        }
    }

    /**
     * returns status of the connectiton
     * @return boolean
     */
    final public function isConnected()
    {
        return $this->connected;
    }

    /**
     * Registers a class to send callbacks, must be compliant to IrcListener
     * @param Object $class
     */
    final public function registerCallbackClass($class)
    {
        $this->callbackClasses[] = $class;
    }

    private function processRead($socket)
    {
        $data = @socket_read($socket, 4096, PHP_NORMAL_READ);

        if ($data === false) {
            $this->throwError();
        }
        $data = trim($data);

        if (empty($data)) {
            return true;
        }

        //echo "irc: " . $data . "\n";

        $needle = "/^(?:[:](\S+) )?(\S+)(?: (?!:)(.+?))?(?: [:](.+))?$/";
        preg_match($needle, $data, $messages);

        switch ($messages[2]) {
            case "221":
            case "266":
                $this->joinChannels();
                $this->sendCallback(self::onConnect, array($this));
                break;
            case "PRIVMSG":
                if ($this->getIrcNick($messages[1]) == $this->config->nickname) {
                    echo "message from self, ignore\n";
                    break;
                }
                if ($messages[3] == $this->config->channel) {
                    $params = array($this, $this->config->channel, $messages[1], $messages[4]);
                    $this->sendCallback(self::onPublicChat, $params);
                }
                if ($messages[3] == $this->config->nickname) {
                    $params = array($this, $messages[1], $messages[4]);
                    $this->sendCallback(self::onPrivateMessage, $params);
                }
                break;
            case "PING":
                $this->pong($messages[4]);

            default:
                break;
        }
    }

    public function getIrcNick($string)
    {
        list ($nick, $host) = explode("!", $string, 2);
        return $nick;
    }

    public function getIrcHost($string)
    {
        list ($nick, $host) = explode("!", $string, 2);
        return $host;
    }

    private function sendCallback($callback, $params)
    {
        foreach ($this->callbackClasses as $class) {
            if (method_exists($class, $callback)) {
                call_user_func_array(array($class, $callback), $params);
            }
        }
    }

    private function pong($server)
    {
        $this->sendCommand("PONG", $server);
    }

    /**
     * Sends message to irc as query
     * @param string $to
     * @param string $message
     */
    public function sendPrivateMessage($to, $message)
    {
        $this->sendCommand("PRIVMSG", $to . " :" . $message);
    }

    /**
     * Sends message to irc as public message
     * @param string $message
     */
    public function sendPublicChat($message)
    {
        if ($this->isJoinedToChannel) {
            $this->sendCommand("PRIVMSG", $this->config->channel . " :" . $message);
        }
    }

    private function joinChannels()
    {
        $key = "";
        if (!empty($this->config->channelKey)) {
            $key = " " . $this->config->channelKey;
        }
        $channel = $this->config->channel . $key;
        $this->sendCommand("JOIN", $channel);
        $this->isJoinedToChannel = true;
    }

    private function sendCommand($command, $params)
    {
        $string = strtoupper($command) . " " . $params;
        $this->send($string);
    }

    private function send($string)
    {
        $write = socket_write($this->socket, $string . BR);
        echo "write: $string \n";
        if ($write === false) {
            $this->throwError();
        }
    }

    private function throwError()
    {
        if (is_resource($this->socket)) {
            $errno = \socket_last_error($this->socket);
            $text = \socket_strerror($errno);
            \socket_clear_error();
            @\socket_close($this->socket);
            $this->connected = false;
            $this->socket = null;
            throw new IrcException($text, $errno);
        }
        throw new IrcException("Generic error. Something is really went wrong.'");
    }

}
