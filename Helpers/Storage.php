<?php

/**
 * @author      Oliver de Cramer (oliverde8 at gmail.com)
 * @copyright    GNU GENERAL PUBLIC LICENSE
 *                     Version 3, 29 June 2007
 *
 * PHP version 5.3 and above
 *
 * LICENSE: This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see {http://www.gnu.org/licenses/}.
 */

namespace ManiaLivePlugins\eXpansion\Helpers;

use ManiaLib\Utils\Singleton;
use ManiaLive\DedicatedApi\Callback\base64;
use ManiaLive\DedicatedApi\Callback\SMapInfo;
use ManiaLive\DedicatedApi\Callback\SPlayerInfo;
use ManiaLive\DedicatedApi\Callback\SPlayerRanking;
use ManiaLive\DedicatedApi\Callback\StatsName;
use ManiaLive\DedicatedApi\Callback\StatusCode;
use ManiaLive\Event\Dispatcher;
use ManiaLive\DedicatedApi\Callback\Event as ServerEvent;
use ManiaLive\DedicatedApi\Callback\Listener as ServerListener;
use ManiaLivePlugins\eXpansion\Core\RelayLink;
use ManiaLivePlugins\eXpansion\Database\Structures\DbPlayer;
use Maniaplanet\DedicatedServer\Structures\Version;
use ManiaLive\DedicatedApi\Config as DedicatedConfig;

class Storage extends Singleton implements \ManiaLive\Event\Listener
{

	const TITLE_SIMPLE_TM = 'TM';

	const TITLE_SIMPLE_SM = 'SM';

	/**
	 * @var \Maniaplanet\DedicatedServer\Connection
	 */
	private $connection;

	/**
	 * @var DbPlayer[]
	 */
	public $dbPlayers = array();

	/**
	 * @var \ManiaLive\Data\Storage
	 */
	private $storage;

	/**
	 * The version of the dedicated on which the system is running
	 *
	 * @var Version
	 */
	public $version;

	/**
	 * The simple title the environment of the track refers to
	 *
	 * @var String
	 */
	public $simpleEnviTitle;

	/**
	 * Is this server a relay server or a game server
	 *
	 * @var bool
	 */
	public $isRelay;

	/**
	 * 	base map type for the server
	 * @var type 
	 */
	public $baseMapType = null;

	/**
	 * @var RelayLink
	 */
	public $relay;

	/**
	 * is this eXpansion running locally on server (true)
	 * or 
	 * is this expansion running remotelly from server (false)
	 * 
	 * @var boolean 
	 */
	public $isRemoteControlled = false;

	private $startTime;

	private $dediUpTime;

	protected function __construct()
	{

		$this->connection = Singletons::getInstance()->getDediConnection();

		$this->storage = \ManiaLive\Data\Storage::getInstance();

		$this->version = $this->connection->getVersion();

		$this->isRelay = $this->connection->isRelayServer();

		$this->simpleEnviTitle = $this->getSimpleTitleByEnvironment($this->storage->currentMap->environnement);

		$this->baseMapType = $this->getSimpleMapType($this->storage->currentMap->mapType);

		$this->relay = RelayLink::getInstance();

		$this->startTime = time();

		if (DedicatedConfig::getInstance()->host == "localhost" || DedicatedConfig::getInstance()->host == "127.0.0.1")
			$this->isRemoteControlled = false;
		else
			$this->isRemoteControlled = true;
		
		$this->dediUpTime = $this->connection->getNetworkStats()->uptime;
	}

	protected function getSimpleMapType($type)
	{
		$parts = explode("\\", $type);
		if (is_array($parts)) {
			return end($parts);
		}
		else {
			return $type;
		}
	}

	protected function getSimpleTitleByEnvironment($enviName)
	{
		if ($enviName == "Stadium" || $enviName == "Valley" || $enviName == "Canyon") {
			return self::TITLE_SIMPLE_TM;
		}
		else {
			return self::TITLE_SIMPLE_SM;
		}
	}

	/**
	 * @param $login
	 *
	 * @return DbPlayer|null
	 */
	public function getDbPlayer($login)
	{
		if (isset($this->dbPlayers[$login])) {
			return $this->dbPlayers[$login];
		}
		else {
			return null;
		}
	}

	public function getExpansionUpTime()
	{
		return time() - $this->startTime;
	}

	public function getDediUpTime()
	{
		return $this->getExpansionUpTime() + $this->dediUpTime;
	}

}
