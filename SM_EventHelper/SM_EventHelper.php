<?php

namespace ManiaLivePlugins\eXpansion\SM_EventHelper;

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

/**
 * Description of Widgets_Speedometer
 *
 * @author Reaby
 */
class SM_EventHelper extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

	private $widget;

	public function exp_onReady()
	{
		$aHandler = \ManiaLive\Gui\ActionHandler::getInstance();

		Gui\EventHelper::$actions['checkpoint'] = $aHandler->createAction(array($this, "invokeCheckpoint"));
		Gui\EventHelper::$actions['finish'] = $aHandler->createAction(array($this, "invokeFinish"));
		
		$this->enableDedicatedEvents();
		$this->widget = Gui\EventHelper::Create(null);
		$this->widget->show();
	}

	public function onEndMatch($rankings, $winnerTeamOrMap)
	{
		$this->widget->hide();
	}

	public function onBeginMap($map, $warmUp, $matchContinuation)
	{
		$this->widget->show();
	}

	public function invokeCheckpoint($login, $entries)
	{
		$timeOrScore = $entries['timeOrScore'];
		$cpIndex =  $entries['index'];
		echo "onCheckpoint:" . $login . "\n";
		print_r($entries);	
	}
	
	public function invokeFinish($login, $entries)
	{
		$timeOrScore = $entries['timeOrScore'];
		$cpIndex = $entries['index'];
		
		echo "onFinish;" . $login . "\n";
		print_r($entries);
	}
	
	public function onBeginMatch()
	{
		$this->widget->show();
	}

	public function exp_onUnload()
	{
		$this->widget = null;
		Gui\EventHelper::EraseAll();
	}

}
