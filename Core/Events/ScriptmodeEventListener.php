<?php

/**
 * @author       Petri Järvisalo (petri.jarvisalo at gmail.com)
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

    public function LibXmlRpc_BeginMatch($number);

    public function LibXmlRpc_LoadingMap($number);

    public function LibXmlRpc_BeginMap($number);

    public function LibXmlRpc_BeginSubmatch($number);

    public function LibXmlRpc_BeginRound($number);

    public function LibXmlRpc_BeginTurn($number);

    public function LibXmlRpc_EndTurn($number);

    public function LibXmlRpc_EndRound($number);

    public function LibXmlRpc_EndSubmatch($number);

    public function LibXmlRpc_EndMap($number);

    public function LibXmlRpc_EndMatch($number);

    public function LibXmlRpc_BeginWarmUp();

    public function LibXmlRpc_EndWarmUp();

    /* storm common */

    public function LibXmlRpc_Rankings($array);

    public function LibXmlRpc_Scores($MatchScoreClan1, $MatchScoreClan2, $MapScoreClan1, $MapScoreClan2);

    public function LibXmlRpc_PlayerRanking($rank, $login, $nickName, $teamId, $isSpectator, $isAway, $currentPoints, $zone);

    public function LibXmlRpc_OnCapture($login);

    public function WarmUp_Status($status);

    public function LibAFK_IsAFK($login);

    public function LibAFK_Properties($idleTimelimit, $spawnTimeLimit, $checkInterval, $forceSpec);

    /* tm common */

    public function LibXmlRpc_OnStartLine($login);

    public function LibXmlRpc_OnWayPoint($login, $blockId, $time, $cpIndex, $isEndBlock, $lapTime, $lapNb, $isLapEnd);

    public function LibXmlRpc_OnGiveUp($login);

    public function LibXmlRpc_OnRespawn($login);

    public function LibXmlRpc_OnStunt(
        $login,
        $points,
        $combo,
        $totalScore,
        $factor,
        $stuntname,
        $angle,
        $isStraight,
        $isReversed,
        $isMasterJump
    );

    /* more events */

    public function LibXmlRpc_BeginPlaying();

    public function LibXmlRpc_EndPlaying();

    public function LibXmlRpc_UnloadingMap($mapNumber);

    public function LibXmlRpc_BeginPodium();

    public function LibXmlRpc_EndPodium();

    public function LibXmlRpc_OnStartCountdown($login);

    /* generated */

    public function LibXmlRpc_Callbacks($value);

    public function LibXmlRpc_CallbackHelp($value);

    public function LibXmlRpc_BlockedCallbacks($value);

    public function LibXmlRpc_BeginServer();

    public function LibXmlRpc_BeginServerStop();

    public function LibXmlRpc_BeginMatchStop($value);

    public function LibXmlRpc_BeginMapStop($value);

    public function LibXmlRpc_BeginSubmatchStop($value);

    public function LibXmlRpc_BeginRoundStop($value);

    public function LibXmlRpc_BeginTurnStop($value);

    public function LibXmlRpc_EndTurnStop($value);

    public function LibXmlRpc_EndRoundStop($value);

    public function LibXmlRpc_EndSubmatchStop($value);

    public function LibXmlRpc_EndMapStop($value);

    public function LibXmlRpc_EndMatchStop($value);

    public function LibXmlRpc_EndServer();

    public function LibXmlRpc_EndServerStop();

    public function LibXmlRpc_PlayersRanking($value);

    public function LibXmlRpc_PlayersScores($value);

    public function LibXmlRpc_PlayersTimes($value);

    public function LibXmlRpc_TeamsScores($value);

    public function LibXmlRpc_WarmUp($value);

    public function LibXmlRpc_TeamsMode($value);

    public function UI_Properties($value);
}
