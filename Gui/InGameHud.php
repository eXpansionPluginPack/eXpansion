<?php

/*
 * Copyright (C) 2014 Reaby
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace ManiaLivePlugins\eXpansion\Gui;

/**
 * Description of InGameHud
 *
 * @author Reaby
 */
class InGameHud extends \ManiaLib\Utils\Singleton
{

	public $mapInfo = true;

	public $opponentInfo = true;

	public $chat = true;

	public $chatLines = 7;

	public $chatOffset = array(0, 0);

	public $checkpointList = true;

	public $checkpointListPosition = array(40, -90, 5);

	public $roundScores = true;

	public $roundScoresPosition = array(104, 14, 5);

	/** @var boolean Race time left displayed at the bottom right of the screen */
	public $countdown = true;

	/** @var float[] position of Race time left displayed at the bottom right of the screen */
	public $countdownPosition = array(154, -57, 5);

	/** @var boolean 3, 2, 1, Go! message displayed on the middle of the screen when spawning */
	public $go = true;

	/** @var boolean  Current race chrono displayed at the bottom center of the screen  */
	public $chrono = true;

	public $chronoPosition = array(0, -80, 5);

	/** @var boolean  Speed and distance raced displayed in the bottom right of the screen */
	public $speedAndDistance = true;

	public $speedAndDistancePosition = array(158, -79.5, 5);

	/** @var boolean Previous and best times displayed at the bottom right of the screen */
	public $personalBest = true;

	/** @var float[] Position of Previous and best times displayed at the bottom right of the screen */
	public $personalBestPosition = array(158, -61, 5);

	/** @var boolean Current position in the map ranking displayed at the bottom right of the screen */
	public $racePosition = true;

	/** @var boolean Checkpoint time information displayed in the middle of the screen when crossing a checkpoint */
	public $checkpointTime = true;

	public $checkpointTimePosition = array(-8, 31.8, -10);

	/** @var boolean The avatar of the last player speaking in the chat displayed above the chat */
	public $chatAvatar = true;

	/** @var boolean Warm-up progression displayed on the right of the screen during warm-up */
	public $warmup = true;

	public $wampupPosition = array(170, 27, 0);

	function update()
	{
		/** @var  \Maniaplanet\DedicatedServer\Connection $connection */
		$connection = \ManiaLivePlugins\eXpansion\Helpers\Singletons::getInstance()->getDediConnection();

		$connection->triggerModeScriptEvent("UI_SetProperties", $this->genXml());
	}

	private function genXml()
	{
		$xml = '<ui_properties>
					  <map_info visible="' . $this->getBool("mapInfo") . '" />
				      <opponents_info visible="' . $this->getBool("opponentsInfo") . '" />
					   <chat visible="' . $this->getBool("chat") . '" offset="' . $this->getPosition('chatOffset') . '" linecount="' . $this->chatLines . '" />
 					   <checkpoint_list visible="' . $this->getBool("checkpointList") . '" pos="' . $this->getPosition('checkpointListPosition') . '" />
					   <round_scores visible="' . $this->getBool("roundScores") . '" pos="' . $this->getPosition('roundScoresPosition') . '" />					   
					   <countdown visible="' . $this->getBool("countdown") . '" pos="' . $this->getPosition("countdownPosition") . '" />
					   <go visible="' . $this->getBool("go") . '" />
					   <chrono visible="' . $this->getBool("chrono") . '" pos="' . $this->getPosition("chronoPosition") . '" />  
					   <speed_and_distance visible="' . $this->getBool("speedAndDistance") . '" pos="' . $this->getPosition("speedAndDistancePosition") . '" />
					   <personal_best_and_rank visible="' . $this->getBool("personalBest") . '" pos="' . $this->getPosition("personalBestPosition") . '" />
					   <position visible="' . $this->getBool("racePosition") . '" />
					   <checkpoint_time visible="' . $this->getBool("checkpointTime") . '" pos="' . $this->getPosition("checkpointTimePosition") . '" />
					   <chat_avatar visible="' . $this->getBool("chatAvatar") . '" />		
					  <warmup visible="' . $this->getBool("warmup") . '" pos="' . $this->getPosition("wampupPosition") . '" />
				</ui_properties>';


		return $xml;
	}

	private function getBool($var)
	{
		if ($this->{$var} === true) {
			return "true";
		}
		return "false";
	}

	private function getPosition($var)
	{
		if (count($this->{$var}) == 3) {
			return $this->getNumber($this->{$var}[0]) . " " . $this->getNumber($this->{$var}[1]) . " " . $this->getNumber($this->{$var}[2]);
		}
		if (count($this->{$var}) == 2) {
			return $this->getNumber($this->{$var}[0]) . " " . $this->getNumber($this->{$var}[1]);
		}
	}

	private function getNumber($number)
	{
		return number_format((float) $number, 2, '.', '');
	}

}
