<?php

/*
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

/**
 * Description of Connection
 *
 * @author Petri
 */
class IrcConnection extends \ManiaLib\Utils\Singleton
{

    private $irc;

    /**
     *
     * @param \ManiaLivePlugins\eXpansion\IRC\Classes\IrcConfig $config
     */
    public function connect(IrcConfig $config)
    {
        $this->irc = new IrcBot($config);
    }

    public function registerCallbackClass($class)
    {
        $this->irc->registerCallbackClass($class);
    }

    public function disconnect()
    {
        if ($this->irc->isConnected()) {
            $this->irc->disconnect();
        }
    }

    public function sendChat($message)
    {
        $this->irc->sendPublicChat($message);
    }

    public function onTick()
    {
        if ($this->irc instanceof IrcBot && $this->irc->isConnected()) {
            $this->irc->onTick();
        }
    }

    public function getIrcNick($string)
    {
        return $this->irc->getIrcNick($string);
    }

}
