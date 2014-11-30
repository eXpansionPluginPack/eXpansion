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

	/**  for testing stuff */
	const ForceRemote = false;

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
	 * Cached titleId value
	 * @var string
	 */
	public $titleId;

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

	/** @var \Maniaplanet\DedicatedServer\Structures\PlayerDetailedInfo */
	public $serverAccount = null;

	/** @var string Just the country in which the server is */
	public $serverCountry = '';

	/** @var string Just php version without compilation iformation */
	public $cleanPhpVersion = '';

	/** @var string Os of the server */
	public $serverOs = '';

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

		$this->relay = RelayLink::getInstance();
		
		$this->storage = \ManiaLive\Data\Storage::getInstance();

		$this->version = $this->connection->getVersion();

		$this->titleId = $this->version->titleId;

		$this->isRelay = $this->connection->isRelayServer();

		$this->simpleEnviTitle = $this->getSimpleTitleByEnvironment($this->storage->currentMap->environnement);

		$this->baseMapType = $this->getSimpleMapType($this->storage->currentMap->mapType);
		
		$this->startTime = time();

		$this->serverAccount = $this->connection->getDetailedPlayerInfo($this->storage->serverLogin);

		if (DedicatedConfig::getInstance()->host == "localhost" || DedicatedConfig::getInstance()->host == "127.0.0.1")
			$this->isRemoteControlled = false;
		else
			$this->isRemoteControlled = true;

		if (self::ForceRemote) {
			$this->isRemoteControlled = true;
			$this->connection->chatSend('[notice] $$Exp_storage->isRemoteControlled is forced to True!', null, true);
		}
		$this->dediUpTime = $this->connection->getNetworkStats()->uptime;

		$formatter = \ManiaLivePlugins\eXpansion\Gui\Formaters\Country::getInstance();
		$this->serverCountry = $formatter->format($this->serverAccount->path);

		$version = explode('-', phpversion());
		$this->cleanPhpVersion = $version[0];

		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$this->serverOs = "Windows";
		} else if (strtoupper(substr(PHP_OS, 0, 3)) === 'MAC') {
			$this->serverOs = "Mac";
		} else {
			$this->serverOs = "Linux";
		}
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
