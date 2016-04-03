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

namespace ManiaLivePlugins\eXpansion\SM_PlatformScores;


use ManiaLivePlugins\eXpansion\Core\types\BasicPlugin;
use ManiaLivePlugins\eXpansion\LocalRecords\LocalBase;
use ManiaLivePlugins\eXpansion\LocalRecords\LocalRecords;

/**
 * Class LocalScores
 *
 * @package ManiaLivePlugins\eXpansion\SM\PlatformScores
 */
class SM_PlatformScores extends LocalBase
{

    private $lastCpNum = array();
    private $cpScores = array();

    public function exp_onLoad()
    {
        $this->enableScriptEvents("LibXmlRpc_OnWayPoint");
    }

    public function exp_onReady()
    {
        parent::exp_onReady();
    }

    public function LibXmlRpc_OnWayPoint(
        $login, $blockId, $time, $cpIndex, $isEndBlock, $lapTime, $lapNb, $isLapEnd
    )
    {
        if ($time > 0) {
            if ($isEndBlock) {
                $this->addRecord($login, $time, 0, $this->cpScores[$login]);

                $this->cpScores[$login] = array();
            } else {
                if (!isset($this->lastCpNum[$login]) || $this->lastCpNum[$login] < $cpIndex) {
                    $this->cpScores[$login][$cpIndex] = $time;
                } else {
                    //Respawned
                    $this->cpScores[$login] = array();
                }

                $this->lastCpNum[$login] = $cpIndex;
            }
        }
    }

    public function onEndMatch($rankings, $winnerTeamOrMap)
    {
        foreach ($this->cpScores as $login => $scores) {
            if (!empty($scores) && isset($this->lastCpNum[$login]) && isset($scores[$this->lastCpNum[$login]]))
                $this->addRecord($login, $scores[$this->lastCpNum[$login]], 0, $scores);
        }

        parent::onEndMatch($rankings, $winnerTeamOrMap);
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
        return $newTime >= $oldTime;
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