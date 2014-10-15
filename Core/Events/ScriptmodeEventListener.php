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

	function Script_onBeginMatch($number);

	function Script_onLoadingMap($number);

	function Script_onBeginMap($number);

	function Script_onBeginSubmatch($number);

	function Script_onBeginRound($number);

	function Script_onBeginTurn($number);

	function Script_onEndTurn($number);

	function Script_onEndRound($number);

	function Script_onEndSubmatch($number);

	function Script_onEndMap($number);

	function Script_onEndMatch($number);

	function Script_onBeginWarmUp();

	function Script_onEndWarmUp();

	/* storm common */

	function Script_Rankings($array);

	function Script_Scores($MatchScoreClan1, $MatchScoreClan2, $MapScoreClan1, $MapScoreClan2);

	function Script_PlayerRanking($rank, $login, $nickName, $teamId, $isSpectator, $isAway, $currentPoints, $zone);

	function Script_onCapture($login);

	function WarmUp_Status($status);

	function LibAFK_IsAFK($login);

	function LibAFK_Properties($idleTimelimit, $spawnTimeLimit, $checkInterval, $forceSpec);

	/* tm common */

	function Script_onStartLine($login);

	function Script_onWayPoint($login, $blockId, $time, $cpIndex, $isEndBlock, $lapTime, $lapNb, $isLapEnd);

	function Script_onGiveUp($login);

	function Script_onRespawn($login);

	function Script_onStunt($login, $points, $combo, $totalScore, $factor, $stuntname, $angle, $isStraight, $isReversed, $isMasterJump);

	/* more events */

	function Script_onBeginPlaying();

	function Script_onEndPlaying();

	function Script_onUnloadingMap($mapNumber);

	function Script_onBeginPodium();

	function Script_onEndPodium();

	function Script_onStartCountdown($login);
}

?>
