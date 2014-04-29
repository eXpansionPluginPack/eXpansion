<?php

namespace ManiaLivePlugins\eXpansion\Core\Events;

use \ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks;

class ScriptmodeEvent extends \ManiaLive\Event\Event {
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

    protected $params;

    function __construct($onWhat) {
	parent::__construct($onWhat);
	$params = func_get_args();
	array_shift($params);
	$this->params = $params;
    }

    function fixBooleans(&$array) {
	foreach ($array as $key => $value) {
	    if ($value == "True")
		$array[$key] = true;
	    if ($value == "False")
		$array[$key] = false;
	}
    }

    function fireDo($listener) {
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
		$listener->meLibXmlRpc_EndSubmatch($array[0]);
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
		// Example : ["Login", "#123456", "21723", "7", "False", "6164", "1", "False"]		
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
	}
	return;
    }

}
