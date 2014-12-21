<?php

/**
 * @author       Oliver de Cramer (oliverde8 at gmail.com)
 * @author       Petri Järvisalo (petri.jarvisalo at gmail.com)
 *
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

namespace ManiaLivePlugins\eXpansion\Core;

use ManiaLive\DedicatedApi\Callback\base64;
use ManiaLive\DedicatedApi\Callback\Event as ServerEvent;
use ManiaLive\DedicatedApi\Callback\Listener as ServerEventListener;
use ManiaLive\DedicatedApi\Callback\SMapInfo;
use ManiaLive\DedicatedApi\Callback\SPlayerInfo;
use ManiaLive\DedicatedApi\Callback\SPlayerRanking;
use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\Core\Events\ScriptmodeEvent;
use Maniaplanet\DedicatedServer\Connection;
use ReflectionClass;

/**
 * Transforms script callbacks
 *
 * @package ManiaLivePlugins\eXpansion\Core
 */
class ScriptEventDispatcher implements ServerEventListener
{

	/**
	 * @var Connection
	 */
	private $connection;

	private $constants;

	function __construct($connection)
	{
		$this->connection = $connection;
		Dispatcher::register(ServerEvent::getClass(), $this, ServerEvent::ON_MODE_SCRIPT_CALLBACK);
		$rc = new ReflectionClass('ManiaLivePlugins\\eXpansion\\Core\\Events\\ScriptmodeEvent');
		$this->constants = $rc->getConstants();
	}

	public function onModeScriptCallback($param1, $param2)
	{

//echo "\n" . $param1 . "\n";
//print_r($param2);
	//	$this->connection->chatSendServerMessage('$0d0' . $param1);
		if (array_key_exists($param1, $this->constants)) {
			Dispatcher::dispatch(new ScriptmodeEvent($this->constants[$param1], $param2));
		}
		else {
			\ManiaLivePlugins\eXpansion\Helpers\Helper::log("[Core] Notice: Script mode callback not implemented: " . $param1);
		}
	}

	/**
	 * Method called when a Player join the server
	 *
	 * @param string $login
	 * @param bool $isSpectator
	 */
	function onPlayerConnect($login, $isSpectator)
	{
		
	}

	/**
	 * Method called when a Player quit the server
	 *
	 * @param string $login
	 */
	function onPlayerDisconnect($login, $disconnectionReason)
	{

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
	 * Method called when a map begin
	 *
	 * @param SMapInfo $map
	 * @param bool $warmUp
	 * @param bool $matchContinuation
	 */
	function onBeginMap($map, $warmUp, $matchContinuation)
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
	 * Method called when player info changed
	 *
	 * @param SPlayerInfo $playerInfo
	 */
	function onPlayerInfoChanged($playerInfo)
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
	 * Method called when the player in parameter has changed its allies
	 *
	 * @param string $login
	 */
	function onPlayerAlliesChanged($login)
	{

	}

}
