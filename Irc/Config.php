<?php

/*
 * ---------------------------------------------------------------------
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * ---------------------------------------------------------------------
 * You are allowed to change things or use this in other projects, as
 * long as you leave the information at the top (name, date, version,
 * website, package, author, copyright) and publish the code under
 * the GNU General Public License version 3.
 * ---------------------------------------------------------------------
 */

namespace ManiaLivePlugins\eXpansion\Irc;

class Config extends \ManiaLib\Utils\Singleton {

    public $hostname = 0;
    public $server = "fi.quakenet.org";
    public $port = 6667;
    public $serverPass = "";
    public $realname = 'ManiaPlanet bot';
    public $nickname = 'driftbot';
    public $ident = 'driftbot';
    public $channel = "#driftstation";
    public $channelKey = "";
    public $allowedIrcLogins = array();
	
    public $plugins = array("AdminPrivateTriggers");
    

}

?>