<?php

namespace ManiaLivePlugins\eXpansion\Core\Events;

use \ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks;

class ScriptmodeEvent extends \ManiaLive\Event\Event
{
	/* general */

	const LibXmlRpc_BeginMatch = 0x1;

	const LibXmlRpc_LoadingMap = 0x2;

	const LibXmlRpc_BeginMap = 0x3;

	const LibXmlRpc_BeginSubmatch = 0x4;

	const LibXmlRpc_BeginRound = 0x5;

	const LibXmlRpc_BeginTurn = 0x6;

	const LibXmlRpc_EndTurn = 0x7;

	const LibXmlRpc_EndRound = 0x8;

	const LibXmlRpc_EndSubmatch = 0x9;

	const LibXmlRpc_EndMap = 0x10;

	const LibXmlRpc_EndMatch = 0x11;

	const LibXmlRpc_BeginWarmUp = 0x12;

	const LibXmlRpc_EndWarmUp = 0x13;

	/* storm common */

	const LibXmlRpc_Rankings = 0x14;

	const LibXmlRpc_Scores = 0x15;

	const LibXmlRpc_PlayerRanking = 0x16;

	const WarmUp_Status = 0x17;

	const LibAFK_IsAFK = 0x18;

	const LibAFK_Properties = 0x19;

	/* tm common */

	const LibXmlRpc_OnStartLine = 0x20;

	const LibXmlRpc_OnWayPoint = 0x21;

	const LibXmlRpc_OnGiveUp = 0x22;

	const LibXmlRpc_OnRespawn = 0x23;

	const LibXmlRpc_OnStunt = 0x24;

	/* more events */

	const LibXmlRpc_OnCapture = 0x25;

	const LibXmlRpc_BeginPlaying = 0x26;

	const LibXmlRpc_EndPlaying = 0x27;

	const LibXmlRpc_UnloadingMap = 0x28;

	const LibXmlRpc_BeginPodium = 0x29;

	const LibXmlRpc_EndPodium = 0x30;

	const LibXmlRpc_OnStartCountdown = 0x31;

	protected $params;

	function __construct($onWhat)
	{
		parent::__construct($onWhat);
		$params = func_get_args();
		array_shift($params);
		$this->params = $params;
	}

	function fixBooleans(&$array)
	{
		foreach ($array as $key => $value) {
			if ($value == "True")
				$array[$key] = true;
			if ($value == "False")
				$array[$key] = false;
		}
	}

	function fireDo($listener)
	{
		$p = $this->params;
		$array = $p[0];
		$this->fixBooleans($array);

		switch ($this->onWhat) {
			case self::LibXmlRpc_BeginMatch:
				$listener->Script_onBeginMatch($array[0]);
				break;
			case self::LibXmlRpc_LoadingMap:
				$listener->Script_onLoadingMap($array[0]);
				break;
			case self::LibXmlRpc_BeginMap:
				$listener->Script_onBeginMap($array[0]);
				break;
			case self::LibXmlRpc_BeginSubmatch:
				$listener->Script_onBeginSubmatch($array[0]);
				break;
			case self::LibXmlRpc_BeginRound:
				$listener->Script_onBeginRound($array[0]);
				break;
			case self::LibXmlRpc_BeginTurn:
				$listener->Script_onBeginTurn($array[0]);
				break;
			case self::LibXmlRpc_EndTurn:
				$listener->Script_onEndTurn($array[0]);
				break;
			case self::LibXmlRpc_EndRound:
				$listener->Script_onEndRound($array[0]);
				break;
			case self::LibXmlRpc_EndSubmatch:
				$listener->Script_onEndSubmatch($array[0]);
				break;
			case self::LibXmlRpc_EndMap:
				$listener->Script_onEndMap($array[0]);
				break;
			case self::LibXmlRpc_EndMatch:
				$listener->Script_onEndMatch($array[0]);
				break;
			case self::LibXmlRpc_BeginWarmUp:
				$listener->Script_onBeginWarmUp();
				break;
			case self::LibXmlRpc_EndWarmUp:
				$listener->Script_onEndWarmUp();
				break;
			case self::LibXmlRpc_Rankings:
				// Example : ["Login1:Score1;Login2:Score2;Login3:Score3;LoginN:ScoreN"]
				$listener->Script_Rankings($array);
				break;
			case self::LibXmlRpc_Scores:
				// Note : ["MatchScoreClan1", "MatchScoreClan2", "MapScoreClan1", "MapScoreClan2"]
				$listener->Script_Scores($array[0], $array[1], $array[2], $array[3]);
				break;
			case self::WarmUp_Status:
				//  Example : ["True"]
				// * Note : This callback is sent after using the `WarmUp_GetStatus` method
				$listener->WarmUp_Status($array[0]);
				break;
			case self::LibAFK_IsAFK:
				/* Data : An array with the login of the AFK player
				 * Example : ["Login"]
				 * Note : This callback is sent when the AFK library detects an AFK player, it will be sent until the player is forced into spectator mode
				 */
				$listener->LibAFK_IsAFK($array[0]);
				break;
			case self::LibAFK_Properties:
				// Example : ["90000", "15000", "10000", "True"]
				// IdleTimelimit, SpanTimeLimit, CheckInterval, ForceSpec
				$listener->LibAFK_Properties($array[0], $array[1], $array[2], $array[3]);
				break;

			case self::LibXmlRpc_OnStartLine:

				$listener->Script_onStartLine($array[0]);
				break;
			case self::LibXmlRpc_OnWayPoint:
				//			  login  , #blockid , time   ,index, endblock, , laptime, lapCpIndex, lapEnd
				// Example : ["Login", "#123456", "21723", "7", "False", "6164", "1", "False"]		
				// Data : the id of the waypoint block, the current race time, the waypoint number in the race, if the waypoint is the end of the race, the current lap time, the waypoint number in the lap and if the waypoint is the end of the lap.
				$listener->Script_onWayPoint($array[0], $array[1], $array[2], $array[3], $array[4], $array[5], $array[6], $array[7]);
				break;
			case self::LibXmlRpc_OnGiveUp:
				$listener->Script_onGiveUp($array[0]);
				break;
			case self::LibXmlRpc_OnRespawn:
				$listener->Script_onRespawn($array[0]);
				break;
			case self::LibXmlRpc_OnStunt:
				$listener->Script_onStunt($array[0], $array[1], $array[2], $array[3], $array[4], $array[5], $array[6], $array[7], $array[8], $array[9]);
				break;
			case self::LibXmlRpc_PlayerRanking:
				// * Note : [Rank, Login, NickName, TeamId, IsSpectator, IsAway, BestTime, Zone]
				$listener->Script_PlayerRanking($array[0], $array[1], $array[2], $array[3], $array[4], $array[5], $array[6], $array[7]);
				break;
			case self::LibXmlRpc_OnCapture:
				// * Note : Login
				$listener->Script_onCapture($array[0]);
				break;
			case self::LibXmlRpc_BeginPlaying:
				$listener->Script_onBeginPlaying();
				break;
			case self::LibXmlRpc_EndPlaying:
				$listener->Script_onEndPlaying();
				break;
			case self::LibXmlRpc_UnloadingMap:
				// * Note: An array with the number of the map
				$listener->Script_onUnloadingMap($array[0]);
				break;
			case self::LibXmlRpc_BeginPodium:
				$listener->Script_onBeginPodium();
				break;
			case self::LibXmlRpc_EndPodium:
				$listener->Script_onEndPodium();
				break;
			case self::LibXmlRpc_OnStartCountdown:
				$listener->Script_onStartCountdown($array[0]);
				break;
		}
	}

}
