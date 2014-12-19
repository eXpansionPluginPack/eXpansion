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

namespace ManiaLivePlugins\eXpansion\TM_EventHelper\Gui;

use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget;

/**
 * Description of EventHelper
 *
 * @author Reaby
 */
class EventHelper extends PlainWidget
{
	
	private $script;

	public static $actions = array();
	
	function onConstruct()
	{
		parent::onConstruct();
		$this->setName("TMEventHelper");
		$this->setPosition(0,900);
		
		$this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("TM_EventHelper\Gui\Script");		
		$this->script->setParam("respawnAction", self::$actions['respawn']);
		$this->registerScript($this->script);
	}
	
}
