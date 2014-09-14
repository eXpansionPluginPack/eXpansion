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

namespace ManiaLivePlugins\eXpansion\SM_ObstaclesScores;


use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\Core\types\BasicPlugin;
use ManiaLivePlugins\eXpansion\LocalRecords\LocalBase;
use ManiaLive\DedicatedApi\Callback\Event as ServerEvent;
use ManiaLivePlugins\eXpansion\LocalRecords\LocalRecords;

/**
 * Class LocalScores
 *
 * @package ManiaLivePlugins\eXpansion\SM\PlatformScores
 */
class SM_ObstaclesScores extends LocalBase {

	const PERM_JUMTO = "obstacles:jumpto";

	const CB_JUMPTO = 'Obstacle.JumpTo';

	/**
	 * The last time of the players past the checkpoints
	 *
	 * @var array login => array( int => int)
	 */
	protected $checkpoints = array();


	public function exp_onReady()
	{
		parent::exp_onReady();

		Dispatcher::register(ServerEvent::getClass(), $this, ServerEvent::ON_MODE_SCRIPT_CALLBACK);

		$cmd = AdminGroups::addAdminCommand("jumpto", $this, "jumpto", self::PERM_JUMTO);
		$cmd->setMinParam(1);
	}

	/*public function LibXmlRpc_OnWayPoint(
		$login, $blockId, $time, $cpIndex, $isEndBlock, $lapTime, $lapNb, $isLapEnd
	)
	{
		if($time > 0){
			if($isEndBlock){
				$this->addRecord($login, $time, 0, $this->cpScores[$login]);

				$this->cpScores[$login] = array();
			}else{
				if(!isset($this->lastCpNum[$login]) || $this->lastCpNum[$login] < $cpIndex){
					$this->cpScores[$login][$cpIndex] = $time;
				}else{
					//Respawned
					$this->cpScores[$login] = array();
				}

				$this->lastCpNum[$login] = $cpIndex;
			}
		}
		echo "\nScore : $login: cpindex: $cpIndex with $time \n";
	}*/

	public function onModeScriptCallback($param1, $param2)
	{

		switch ($param1) {
			case 'playerFinish' :
				$params = explode('{:}', $param2);
				$this->addRecord($params[1], $params[0], 0, $this->checkpoints[$params[1]]);
				break;
			case 'OnCheckpoint' :
				$params = json_decode($param2);
				$this->playerCp($params->Player->Login, $params->Run->Time, $params->Run->CheckpointIndex);
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

	public function jumpto($login, $params){
		$param = $login . ";" . $params[0] . ";";
		$this->connection->triggerModeScriptEvent(self::CB_JUMPTO, $param);
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
	public function formatScore($score){
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
	protected function isBetterTime($newTime, $oldTime){
		return $newTime <= $oldTime;
	}

	/**
	 * @param $newTime
	 * @param $oldTime
	 *
	 * @return float|int|number|string
	 */
	protected function secureBy($newTime, $oldTime){
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
	protected function getDbOrderCriteria(){
		return '`record_score` ASC, `record_date` ASC ';
	}

	public function getNbOfLaps(){
		return 1;
	}

	protected function array_sort($array, $on, $order = SORT_DESC){
		return parent::array_sort($array, $on, $order);
	}
}