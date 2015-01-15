<?php

/*
 * Copyright (C) 2015 Reaby
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

namespace ManiaLivePlugins\eXpansion\TM_Scoretable;

use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\TM_Scoretable\Gui\Scoretable\Scoretable;

/**
 * Description of TM_Scoretable
 *
 * @author Reaby
 */
class TM_Scoretable extends ExpPlugin
{

	public function exp_onReady()
	{
		$this->sendScoretable();	
	}

	public function sendScoretable()
	{
		$style = new Scoretable();
		//echo $style->getXml();
		$this->connection->triggerModeScriptEvent("LibScoresTable2_SetStyleFromXml", array("TM", $style->getXml()));
	}

	public function onSettingsChanged(\ManiaLivePlugins\eXpansion\Core\types\config\Variable $var)
	{
		if ($var->getName() == "tm_score_columns" || $var->getName() == "tm_score_lines") {
			$this->sendScoretable();
		}
	}

	public function exp_onUnload()
	{
		parent::exp_onUnload();
	}

}
