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
use ManiaLive\Data\Player;
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

class Storage extends Singleton implements \ManiaLive\Event\Listener, ServerListener
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

	/** @var Player[] */
	public $players = array();

	/** @var Player[] */
	public $spectators = array();

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
		Dispatcher::register(ServerEvent::getClass(), $this, ServerEvent::ON_PLAYER_CONNECT);
		Dispatcher::register(ServerEvent::getClass(), $this, ServerEvent::ON_PLAYER_DISCONNECT);
		Dispatcher::register(ServerEvent::getClass(), $this, ServerEvent::ON_PLAYER_INFO_CHANGED);
		Dispatcher::register(ServerEvent::getClass(), $this, ServerEvent::ON_BEGIN_MAP);

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

		foreach ($this->storage->players as $player) {
			if ($player->isConnected) {
				$this->players[$player->login] = $player->login;
			}
		}
		foreach ($this->storage->spectators as $player) {
			if ($player->isConnected) {
				$this->spectators[$player->login] = $player->login;
			}
		}
	}

	public function onPlayerConnect($login, $isSpectator)
	{
		if ($isSpectator) {
			$this->spectators[$login] = $login;
		} else {
			$this->players[$login] = $login;
		}
	}

	public function onPlayerDisconnect($login, $disconnectionReason = null)
	{
		$this->removePlayer($login);
	}

	public function onBeginMap($map, $warmUp, $matchContinuation)
	{

		$this->players = array();
		foreach ($this->storage->players as $player) {
			if ($player->isConnected) {
				$this->players[$player->login] = $player->login;
			}
		}

		$this->spectators = array();
		foreach ($this->storage->spectators as $player) {
			if ($player->isConnected) {
				$this->spectators[$player->login] = $player->login;
			}
		}
	}

	public function onPlayerInfoChanged($playerInfo)
	{
		$player = \Maniaplanet\DedicatedServer\Structures\PlayerInfo::fromArray($playerInfo);
		$login = $player->login;

		$this->removePlayer($player->login);

		if ($player->pureSpectator) {
			$this->spectators[$login] = $login;
		} else {
			$this->players[$login] = $login;
		}
	}

	private function removePlayer($login)
	{
		if (array_key_exists($login, $this->spectators))
			unset($this->spectators[$login]);
		if (array_key_exists($login, $this->players))
			unset($this->players[$login]);
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

	/**
	 * Method called when a Player chat on the server
	 *
	 * @param int $playerUid
	 * @param string $login
	 * @param string $text
	 * @param bool $isRegistredCmd
	 */
	function onPlayerChat($playerUid, $login, $text, $isRegistredCmd)
	{
	}

	/**
	 * Method called when a Answer to a Manialink Page
	 * difference with previous TM: this is not called if the player doesn't answer, and thus '0' is also a valid answer.
	 *
	 * @param int $playerUid
	 * @param string $login
	 * @param int $answer
	 */
	function onPlayerManialinkPageAnswer($playerUid, $login, $answer, array $entries)
	{
	}

	/**
	 * Method called when the dedicated Method Echo is called
	 *
	 * @param string $internal
	 * @param string $public
	 */
	function onEcho($internal, $public)
	{
	}

	/**
	 * Method called when the server starts
	 */
	function onServerStart()
	{
	}

	/**
	 * Method called when the server stops
	 */
	function onServerStop()
	{
	}

	/**
	 * Method called when the Race Begin
	 */
	function onBeginMatch()
	{
	}

	/**
	 * Method called when the Race Ended
	 * struct of SPlayerRanking is a part of the structure of Maniaplanet\DedicatedServer\Structures\Player object
	 * struct SPlayerRanking
	 * {
	 *    string Login;
	 *    string NickName;
	 *    int PlayerId;
	 *    int Rank;
	 * [for legacy TrackMania modes also:
	 *    int BestTime;
	 *    int[] BestCheckpoints;
	 *    int Score;
	 *    int NbrLapsFinished;
	 *    double LadderScore;
	 * ]
	 * }
	 *
	 * @param SPlayerRanking[] $rankings
	 * @param int|SMapInfo $winnerTeamOrMap Winner team if API version >= 2012-06-19, else the map
	 */
	function onEndMatch($rankings, $winnerTeamOrMap)
	{
	}

	/**
	 * Method called when a map end
	 *
	 * @param SPlayerRanking[] $rankings
	 * @param SMapInfo $map
	 * @param bool $wasWarmUp
	 * @param bool $matchContinuesOnNextMap
	 * @param bool $restartMap
	 */
	function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap)
	{
	}

	/**
	 * Method called on Round beginning
	 */
	function onBeginRound()
	{
	}

	/**
	 * Method called on Round ending
	 */
	function onEndRound()
	{
	}

	/**
	 * Method called when the server status change
	 *
	 * @param int    StatusCode
	 * @param string StatsName
	 */
	function onStatusChanged($statusCode, $statusName)
	{
	}

	/**
	 * Method called when a player cross a checkPoint
	 *
	 * @param int $playerUid
	 * @param string $login
	 * @param int $timeOrScore
	 * @param int $curLap
	 * @param int $checkpointIndex
	 */
	function onPlayerCheckpoint($playerUid, $login, $timeOrScore, $curLap, $checkpointIndex)
	{
	}

	/**
	 * Method called when a player finish a round
	 *
	 * @param int $playerUid
	 * @param string $login
	 * @param int $timeOrScore
	 */
	function onPlayerFinish($playerUid, $login, $timeOrScore)
	{
	}

	/**
	 * Method called when there is an incoherence with a player data
	 *
	 * @param int $playerUid
	 * @param string $login
	 */
	function onPlayerIncoherence($playerUid, $login)
	{
	}

	/**
	 * Method called when a bill is updated
	 *
	 * @param int $billId
	 * @param int $state
	 * @param string $stateName
	 * @param int $transactionId
	 */
	function onBillUpdated($billId, $state, $stateName, $transactionId)
	{
	}

	/**
	 * Method called server receive data
	 *
	 * @param int $playerUid
	 * @param string $login
	 * @param base64 $data
	 */
	function onTunnelDataReceived($playerUid, $login, $data)
	{
	}

	/**
	 * Method called when the map list is modified
	 *
	 * @param int $curMapIndex
	 * @param int $nextMapIndex
	 * @param bool $isListModified
	 */
	function onMapListModified($curMapIndex, $nextMapIndex, $isListModified)
	{
	}

	/**
	 * Method called when the Flow Control is manual
	 *
	 * @param string $transition
	 */
	function onManualFlowControlTransition($transition)
	{
	}

	/**
	 * Method called when a vote change of State
	 *
	 * @param string $stateName can be NewVote, VoteCancelled, votePassed, voteFailed
	 * @param string $login     the login of the player who start the vote if empty the server start the vote
	 * @param string $cmdName   the command used for the vote
	 * @param string $cmdParam  the parameters of the vote
	 */
	function onVoteUpdated($stateName, $login, $cmdName, $cmdParam)
	{
	}

	/**
	 * @param string
	 * @param string
	 */
	function onModeScriptCallback($param1, $param2)
	{
	}

	/**
	 * Method called when the player in parameter has changed its allies
	 *
	 * @param string $login
	 */
	function onPlayerAlliesChanged($login)
	{
	}
}
