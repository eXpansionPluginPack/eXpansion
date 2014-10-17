<?php

namespace ManiaLivePlugins\eXpansion\Core\Events;

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

	/* import from scriptmode */

	const LibXmlRpc_Callbacks = 0x32;

	const LibXmlRpc_CallbackHelp = 0x33;

	const LibXmlRpc_BlockedCallbacks = 0x34;

	const LibXmlRpc_BeginServer = 0x35;

	const LibXmlRpc_BeginServerStop = 0x36;

	const LibXmlRpc_BeginMatchStop = 0x37;

	const LibXmlRpc_BeginMapStop = 0x38;

	const LibXmlRpc_BeginSubmatchStop = 0x39;

	const LibXmlRpc_BeginRoundStop = 0x40;

	const LibXmlRpc_BeginTurnStop = 0x41;

	const LibXmlRpc_EndTurnStop = 0x42;

	const LibXmlRpc_EndRoundStop = 0x43;

	const LibXmlRpc_EndSubmatchStop = 0x44;

	const LibXmlRpc_EndMapStop = 0x45;

	const LibXmlRpc_EndMatchStop = 0x46;

	const LibXmlRpc_EndServer = 0x47;

	const LibXmlRpc_EndServerStop = 0x48;

	const LibXmlRpc_PlayersRanking = 0x49;

	const LibXmlRpc_PlayersScores = 0x50;

	const LibXmlRpc_PlayersTimes = 0x51;

	const LibXmlRpc_TeamsScores = 0x52;

	const LibXmlRpc_WarmUp = 0x53;

	const LibXmlRpc_TeamsMode = 0x54;

	const UI_Properties = 0x55;

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
				$listener->LibXmlRpc_BeginMatch($array[0]);
				break;
			case self::LibXmlRpc_LoadingMap:
				$listener->LibXmlRpc_LoadingMap($array[0]);
				break;
			case self::LibXmlRpc_BeginMap:
				$listener->LibXmlRpc_BeginMap($array[0]);
				break;
			case self::LibXmlRpc_BeginSubmatch:
				$listener->LibXmlRpc_BeginSubmatch($array[0]);
				break;
			case self::LibXmlRpc_BeginRound:
				$listener->LibXmlRpc_BeginRound($array[0]);
				break;
			case self::LibXmlRpc_BeginTurn:
				$listener->LibXmlRpc_BeginTurn($array[0]);
				break;
			case self::LibXmlRpc_EndTurn:
				$listener->LibXmlRpc_EndTurn($array[0]);
				break;
			case self::LibXmlRpc_EndRound:
				$listener->LibXmlRpc_EndRound($array[0]);
				break;
			case self::LibXmlRpc_EndSubmatch:
				$listener->LibXmlRpc_EndSubmatch($array[0]);
				break;
			case self::LibXmlRpc_EndMap:
				$listener->LibXmlRpc_EndMap($array[0]);
				break;
			case self::LibXmlRpc_EndMatch:
				$listener->LibXmlRpc_EndMatch($array[0]);
				break;
			case self::LibXmlRpc_BeginWarmUp:
				$listener->LibXmlRpc_BeginWarmUp();
				break;
			case self::LibXmlRpc_EndWarmUp:
				$listener->LibXmlRpc_EndWarmUp();
				break;
			case self::LibXmlRpc_Rankings:
				// Example : ["Login1:Score1;Login2:Score2;Login3:Score3;LoginN:ScoreN"]
				$listener->LibXmlRpc_Rankings($array);
				break;
			case self::LibXmlRpc_Scores:
				// Note : ["MatchScoreClan1", "MatchScoreClan2", "MapScoreClan1", "MapScoreClan2"]
				$listener->LibXmlRpc_Scores($array[0], $array[1], $array[2], $array[3]);
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

				$listener->LibXmlRpc_OnStartLine($array[0]);
				break;
			case self::LibXmlRpc_OnWayPoint:
				//			  login  , #blockid , time   ,index, endblock, , laptime, lapCpIndex, lapEnd
				// Example : ["Login", "#123456", "21723", "7", "False", "6164", "1", "False"]
				// Data : the id of the waypoint block, the current race time, the waypoint number in the race, if the waypoint is the end of the race, the current lap time, the waypoint number in the lap and if the waypoint is the end of the lap.
				$listener->LibXmlRpc_OnWayPoint($array[0], $array[1], $array[2], $array[3], $array[4], $array[5], $array[6], $array[7]);
				break;
			case self::LibXmlRpc_OnGiveUp:
				$listener->LibXmlRpc_OnGiveUp($array[0]);
				break;
			case self::LibXmlRpc_OnRespawn:
				$listener->LibXmlRpc_OnRespawn($array[0]);
				break;
			case self::LibXmlRpc_OnStunt:
				$listener->LibXmlRpc_OnStunt($array[0], $array[1], $array[2], $array[3], $array[4], $array[5], $array[6], $array[7], $array[8], $array[9]);
				break;
			case self::LibXmlRpc_PlayerRanking:
				// * Note : [Rank, Login, NickName, TeamId, IsSpectator, IsAway, BestTime, Zone]
				$listener->LibXmlRpc_PlayerRanking($array[0], $array[1], $array[2], $array[3], $array[4], $array[5], $array[6], $array[7]);
				break;
			case self::LibXmlRpc_OnCapture:
				// * Note : Login
				$listener->LibXmlRpc_OnCapture($array[0]);
				break;
			case self::LibXmlRpc_BeginPlaying:
				$listener->LibXmlRpc_BeginPlaying();
				break;
			case self::LibXmlRpc_EndPlaying:
				$listener->LibXmlRpc_EndPlaying();
				break;
			case self::LibXmlRpc_UnloadingMap:
				// * Note: An array with the number of the map
				$listener->LibXmlRpc_UnloadingMap($array[0]);
				break;
			case self::LibXmlRpc_BeginPodium:
				$listener->LibXmlRpc_BeginPodium();
				break;
			case self::LibXmlRpc_EndPodium:
				$listener->LibXmlRpc_EndPodium();
				break;
			case self::LibXmlRpc_OnStartCountdown:
				$listener->LibXmlRpc_OnStartCountdown($array[0]);
				break;
			
			/** generated */
			case self::LibXmlRpc_Callbacks:
				$listener->LibXmlRpc_Callbacks($array[0]);
				break;
			case self::LibXmlRpc_CallbackHelp:
				$listener->LibXmlRpc_CallbackHelp($array[0]);
				break;
			case self::LibXmlRpc_BlockedCallbacks:
				$listener->LibXmlRpc_BlockedCallbacks($array[0]);
				break;
			case self::LibXmlRpc_BeginServer:
				$listener->LibXmlRpc_BeginServer($array[0]);
				break;
			case self::LibXmlRpc_BeginServerStop:
				$listener->LibXmlRpc_BeginServerStop($array[0]);
				break;
			case self::LibXmlRpc_BeginMatchStop:
				$listener->LibXmlRpc_BeginMatchStop($array[0]);
				break;
			case self::LibXmlRpc_BeginMapStop:
				$listener->LibXmlRpc_BeginMapStop($array[0]);
				break;
			case self::LibXmlRpc_BeginSubmatchStop:
				$listener->LibXmlRpc_BeginSubmatchStop($array[0]);
				break;
			case self::LibXmlRpc_BeginRoundStop:
				$listener->LibXmlRpc_BeginRoundStop($array[0]);
				break;
			case self::LibXmlRpc_BeginTurnStop:
				$listener->LibXmlRpc_BeginTurnStop($array[0]);
				break;
			case self::LibXmlRpc_EndTurnStop:
				$listener->LibXmlRpc_EndTurnStop($array[0]);
				break;
			case self::LibXmlRpc_EndRoundStop:
				$listener->LibXmlRpc_EndRoundStop($array[0]);
				break;
			case self::LibXmlRpc_EndSubmatchStop:
				$listener->LibXmlRpc_EndSubmatchStop($array[0]);
				break;
			case self::LibXmlRpc_EndMapStop:
				$listener->LibXmlRpc_EndMapStop($array[0]);
				break;
			case self::LibXmlRpc_EndMatchStop:
				$listener->LibXmlRpc_EndMatchStop($array[0]);
				break;
			case self::LibXmlRpc_EndServer:
				$listener->LibXmlRpc_EndServer($array[0]);
				break;
			case self::LibXmlRpc_EndServerStop:
				$listener->LibXmlRpc_EndServerStop($array[0]);
				break;
			case self::LibXmlRpc_PlayersRanking:
				$listener->LibXmlRpc_PlayersRanking($array[0]);
				break;
			case self::LibXmlRpc_PlayersScores:
				$listener->LibXmlRpc_PlayersScores($array[0]);
				break;
			case self::LibXmlRpc_PlayersTimes:
				$listener->LibXmlRpc_PlayersTimes($array[0]);
				break;
			case self::LibXmlRpc_TeamsScores:
				$listener->LibXmlRpc_TeamsScores($array[0]);
				break;
			case self::LibXmlRpc_WarmUp:
				$listener->LibXmlRpc_WarmUp($array[0]);
				break;
			case self::LibXmlRpc_TeamsMode:
				$listener->LibXmlRpc_TeamsMode($array[0]);
				break;
			case self::UI_Properties:
				$listener->UI_Properties($array[0]);
				break;
		}
	}

}
