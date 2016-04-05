<?php
/**
 * @author       Oliver de Cramer (oliverde8 at gmail.com)
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

namespace ManiaLivePlugins\eXpansion\LocalRecords;


class LocalRecords extends LocalBase
{

    /**
     * The last time of the players past the checkpoints
     *
     * @var array login => array( int => int)
     */
    protected $checkpoints = array();

    /**
     * onPlayerFinish()
     * Function called when a player finishes.
     *
     * @param int    $playerUid
     * @param string $login
     * @param int    $timeOrScore
     *
     * @return void
     */
    public function onPlayerFinish($playerUid, $login, $timeOrScore)
    {
        //Checking for valid time
        if (isset($this->storage->players[$login]) && $timeOrScore > 0) {
            $gamemode = self::exp_getCurrentCompatibilityGameMode();

            //If laps mode we need to ignore. Laps has it's own end map event(end finish lap)
            if ($gamemode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS && $this->config->lapsModeCount1lap) //Laps mode has it own on Player finish event
                return;

            $time = microtime();
            //We add the record to the buffer
            $this->addRecord($login, $timeOrScore, $gamemode, $this->checkpoints[$login]);

            if (($this->debug & self::DEBUG_RECPROCESSTIME) == self::DEBUG_RECPROCESSTIME)
                $this->console("[eXp][DEBUG][LocalRecords:RECS]#### NEW RANK IN : " . (microtime() - $time) . "s BAD?");
        }
        //We reset the checkPoints
        $this->checkpoints[$login] = array();

        parent::onPlayerFinish($playerUid, $login, $timeOrScore);
    }

    /**
     * @param \ManiaLive\Data\Player $player
     * @param                        $time
     * @param                        $checkpoints
     * @param int                    $nbLap
     */
    public function onPlayerFinishLap($player, $time, $checkpoints, $nbLap)
    {

        if ($this->config->lapsModeCount1lap && isset($this->storage->players[$player->login]) && $time > 0) {
            $gamemode = self::exp_getCurrentCompatibilityGameMode();

            if ($gamemode != \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS) //Laps mode has it own on Player finish event
                return;

            // if normal map, don't trigger the event for first lap :)
            if ($this->storage->currentMap->nbLaps == 0) {
                //  $this->console("mapNbLaps: " . $this->storage->currentMap->nbLaps);
                return;
            }

            $this->addRecord($player->login, $time, $gamemode, $this->checkpoints[$player->login]);
            $this->checkpoints[$player->login] = array();
        }

        parent::onPlayerFinishLap($player, $time, $checkpoints, $nbLap);
    }


    /**
     * Function called when someone passes a checkpoint.
     *
     * @param $playerUid
     * @param $login
     * @param $score
     * @param $curLap
     * @param $checkpointIndex
     *
     * turn void
     */
    public function onPlayerCheckpoint($playerUid, $login, $score, $curLap, $checkpointIndex)
    {
        $this->checkpoints[$login][$checkpointIndex] = $score;
    }

    /**
     * @param string $login
     * @param bool   $isSpectator
     */
    public function onPlayerConnect($login, $isSpectator)
    {
        parent::onPlayerConnect($login, $isSpectator);

        $this->checkpoints[$login] = array();
    }

    /**
     * @param string $login
     * @param null   $reason
     */
    public function onPlayerDisconnect($login, $reason = null)
    {
        parent::onPlayerDisconnect($login, $reason);

        //Remove all checkpoints data
        $this->checkpoints[$login] = array();
        unset($this->checkpoints[$login]);
    }

    /**
     * @return string
     */
    protected function getScoreType()
    {
        return self::SCORE_TYPE_TIME;
    }

    /**
     * @param $score
     *
     * @return float|int|number|string
     */
    public function formatScore($score)
    {
        $time = \ManiaLive\Utilities\Time::fromTM($score);
        if (substr($time, 0, 2) === "0:") {
            $time = substr($time, 2);
        }

        return $time;
    }

    /**
     * @param $newTime
     * @param $oldTime
     *
     * @return bool
     */
    protected function isBetterTime($newTime, $oldTime)
    {
        return $newTime <= $oldTime;
    }

    /**
     * @param $newTime
     * @param $oldTime
     *
     * @return float|int|number|string
     */
    protected function secureBy($newTime, $oldTime)
    {
        $securedBy = \ManiaLive\Utilities\Time::fromTM($newTime - $oldTime);
        if (substr($securedBy, 0, 3) === "0:0") {
            $securedBy = substr($securedBy, 3);
        } else if (substr($securedBy, 0, 2) === "0:") {
            $securedBy = substr($securedBy, 2);
        }

        return $securedBy;
    }

    /**
     * @return string
     */
    protected function getDbOrderCriteria()
    {
        return '`record_score` ASC, `record_date` ASC ';
    }

    /**
     * getNbOfLaps()
     * Helper function, gets number of laps.
     *
     * @return int $laps
     */
    public function getNbOfLaps()
    {
        if ($this->storage->gameInfos->gameMode != \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT) {
            switch ($this->storage->gameInfos->gameMode) {
                case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_ROUNDS:
                    if ($this->storage->gameInfos->roundsForcedLaps == 0)
                        return $this->storage->currentMap->nbLaps;
                    else
                        return $this->storage->gameInfos->roundsForcedLaps;

                case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM:
                case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP:
                    return $this->storage->currentMap->nbLaps;
                    break;

                case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS:
                    return $this->storage->gameInfos->lapsNbLaps;
                    break;

                default:
                    return 1;
            }
        } else {
            $settings = $this->connection->getModeScriptSettings();
            switch (self::exp_getCurrentCompatibilityGameMode()) {
                case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_ROUNDS:
                case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM:
                case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP:
                case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS:
                    return $settings['S_ForceLapsNb'] == -1 ? 1 : $settings['S_ForceLapsNb'];
                    break;

                default:
                    return 1;
            }
        }
    }
}
