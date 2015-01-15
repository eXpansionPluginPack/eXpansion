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

namespace ManiaLivePlugins\eXpansion\Widgets_TM_topPanel\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer;

class ClockItem extends PanelItem implements ScriptedContainer
{

	public function __construct($title, $value, $sizeX = 20, $StyleorUrl = null, $iconSubStyle = null)
	{
		parent::__construct($title, $value, $sizeX, $StyleorUrl, $iconSubStyle);
		$this->lbl_value->setStyle("TextRaceMessageBig");
		$this->lbl_value->setTextSize(3.5);
		$this->lbl_value->setPosition(9, -3);
		$this->lbl_title->setPosition(9, 1);
	}

	/**
	 * @return Script the script this container needs
	 */
	public function getScript()
	{
		$script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Widgets_TM_topPanel\Gui\Scripts\clock");
		return $script;
	}

}
