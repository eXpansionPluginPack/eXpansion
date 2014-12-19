<?php

namespace ManiaLivePlugins\eXpansion\TM_EventHelper;

use ManiaLive\Event\Dispatcher;
use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\Core\Events\ScriptmodeEvent;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\TM_EventHelper\Gui\EventHelper;

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
 * Description of SM_EventHelper
 *
 * @author Reaby
 */
class TM_EventHelper extends ExpPlugin
{

	private $widget;

	public function exp_onReady()
	{
		$aHandler = ActionHandler::getInstance();

		EventHelper::$actions['respawn'] = $aHandler->createAction(array($this, "invokeRespawn"));

		$this->enableDedicatedEvents();
		$this->widget = EventHelper::Create(null);
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
	
	public function invokeRespawn($login)
	{
		Dispatcher::dispatch(
				new ScriptmodeEvent(ScriptmodeEvent::LibXmlRpc_OnRespawn, array($login))
		);
	}

	public function onBeginMatch()
	{
		$this->widget->show();
	}

	public function exp_onUnload()
	{
		$this->widget = null;
		EventHelper::EraseAll();
	}

}
