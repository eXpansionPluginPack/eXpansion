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

namespace ManiaLivePlugins\eXpansion\TM_DopplerScores;

use ManiaLivePlugins\eXpansion\LocalRecords\LocalBase;

/**
 * Class LocalScores
 *
 * @package ManiaLivePlugins\eXpansion\SM\PlatformScores
 */
class TM_DopplerScores extends LocalBase
{

    /**
     * The last time of the players past the checkpoints
     *
     * @var array login => array( int => int)
     */
    protected $checkpoints = array();

    public function eXpOnReady()
    {
        parent::eXpOnReady();

        $this->enableScriptEvents(array("Doppler_onCheckpoint", "LibXmlRpc_OnWayPoint", "playerFinish"));
    }


    public function eXpOnModeScriptCallback($param1, $param2)
    {

        switch ($param1) {
            case 'playerFinish':
                $params = explode('{:}', $param2);
                $this->addRecord($params[1], $params[0], 0, $this->checkpoints[$params[1]]);
                break;
            case 'LibXmlRpc_OnWayPoint':
                print_r($param2);
                break;
            case 'Doppler_onCheckpoint':
                $params = json_decode($param2);
                print_r($params);
                break;
        }
    }

    /**
     * Function called when someone passes a checkpoint.
     *
     * @param $login
     * @param $score
     * @param $checkpointIndex
     *
     * turn void
     */
    public function playerCp($login, $score, $checkpointIndex)
    {
        $this->checkpoints[$login][$checkpointIndex] = $score;
    }

    /**
     * @param string $login
     * @param bool $isSpectator
     */
    public function onPlayerConnect($login, $isSpectator)
    {
        parent::onPlayerConnect($login, $isSpectator);

        $this->checkpoints[$login] = array();
    }

    /**
     * @param string $login
     * @param null $reason
     */
    public function onPlayerDisconnect($login, $reason = null)
    {
        parent::onPlayerDisconnect($login, $reason);

        //Remove all checkpoints data
        $this->checkpoints[$login] = array();
        unset($this->checkpoints[$login]);
    }

    protected function getScoreType()
    {
        return self::SCORE_TYPE_SCORE;
    }

    public function formatScore($score)
    {
        return $score;
    }

    protected function isBetterTime($newTime, $oldTime)
    {
        return $newTime <= $oldTime;
    }

    protected function secureBy($newTime, $oldTime)
    {
        return ($newTime - $oldTime) . 'points';
    }

    protected function getDbOrderCriteria()
    {
        return '`record_score` DESC, `record_date` ASC ';
    }

    public function getNbOfLaps()
    {
        return 1;
    }

    protected function array_sort($array, $on, $order = SORT_DESC)
    {
        return parent::array_sort($array, $on, $order);
    }
}
