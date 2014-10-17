<?php

/**
 * @author      Petri Järvisalo (petri.jarvisalo at gmail.com)
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

namespace ManiaLivePlugins\eXpansion\Core\Events;

/**
 * Description of 
 *
 * @author reaby
 */
interface ScriptmodeEventListener
{

	function LibXmlRpc_BeginMatch($number);

	function LibXmlRpc_LoadingMap($number);

	function LibXmlRpc_BeginMap($number);

	function LibXmlRpc_BeginSubmatch($number);

	function LibXmlRpc_BeginRound($number);

	function LibXmlRpc_BeginTurn($number);

	function LibXmlRpc_EndTurn($number);

	function LibXmlRpc_EndRound($number);

	function LibXmlRpc_EndSubmatch($number);

	function LibXmlRpc_EndMap($number);

	function LibXmlRpc_EndMatch($number);

	function LibXmlRpc_BeginWarmUp();

	function LibXmlRpc_EndWarmUp();

	/* storm common */

	function LibXmlRpc_Rankings($array);

	function LibXmlRpc_Scores($MatchScoreClan1, $MatchScoreClan2, $MapScoreClan1, $MapScoreClan2);

	function LibXmlRpc_PlayerRanking($rank, $login, $nickName, $teamId, $isSpectator, $isAway, $currentPoints, $zone);

	function LibXmlRpc_OnCapture($login);

	function WarmUp_Status($status);

	function LibAFK_IsAFK($login);

	function LibAFK_Properties($idleTimelimit, $spawnTimeLimit, $checkInterval, $forceSpec);

	/* tm common */

	function LibXmlRpc_OnStartLine($login);

	function LibXmlRpc_OnWayPoint($login, $blockId, $time, $cpIndex, $isEndBlock, $lapTime, $lapNb, $isLapEnd);

	function LibXmlRpc_OnGiveUp($login);

	function LibXmlRpc_OnRespawn($login);

	function LibXmlRpc_OnStunt($login, $points, $combo, $totalScore, $factor, $stuntname, $angle, $isStraight, $isReversed, $isMasterJump);

	/* more events */

	function LibXmlRpc_BeginPlaying();

	function LibXmlRpc_EndPlaying();

	function LibXmlRpc_UnloadingMap($mapNumber);

	function LibXmlRpc_BeginPodium();

	function LibXmlRpc_EndPodium();

	function LibXmlRpc_OnStartCountdown($login);

	/* generated */

	function LibXmlRpc_Callbacks($value);

	function LibXmlRpc_CallbackHelp($value);

	function LibXmlRpc_BlockedCallbacks($value);

	function LibXmlRpc_BeginServer($value);

	function LibXmlRpc_BeginServerStop($value);

	function LibXmlRpc_BeginMatchStop($value);

	function LibXmlRpc_BeginMapStop($value);

	function LibXmlRpc_BeginSubmatchStop($value);

	function LibXmlRpc_BeginRoundStop($value);

	function LibXmlRpc_BeginTurnStop($value);

	function LibXmlRpc_EndTurnStop($value);

	function LibXmlRpc_EndRoundStop($value);

	function LibXmlRpc_EndSubmatchStop($value);

	function LibXmlRpc_EndMapStop($value);

	function LibXmlRpc_EndMatchStop($value);

	function LibXmlRpc_EndServer($value);

	function LibXmlRpc_EndServerStop($value);

	function LibXmlRpc_PlayersRanking($value);

	function LibXmlRpc_PlayersScores($value);

	function LibXmlRpc_PlayersTimes($value);

	function LibXmlRpc_TeamsScores($value);

	function LibXmlRpc_WarmUp($value);

	function LibXmlRpc_TeamsMode($value);

	function UI_Properties($value);
	
}

?>
