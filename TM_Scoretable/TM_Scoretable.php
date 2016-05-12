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

    public function eXpOnReady()
    {
        $this->sendScoretable();
    }

    public function sendScoretable()
    {
        $this->connection->triggerModeScriptEvent("LibScoresTable2_SetStyleFromXml", array("TM", $this->getXML()));
    }

    public function onSettingsChanged(\ManiaLivePlugins\eXpansion\Core\types\config\Variable $var)
    {
        if ($var->getName() == "tm_score_columns" || $var->getName() == "tm_score_lines") {
            $this->sendScoretable();
        }
    }

    public function eXpOnUnload()
    {
        parent::eXpOnUnload();
    }

    protected function getXML()
    {
        //$this->xml = file_get_contents(__DIR__ . "/scores.xml");
        $config = Config::getInstance();

        $x = 140 + (20 * $config->tm_score_columns - 2);
        $y = (8 * $config->tm_score_lines);

        if ($y < 50) {
            $y = 50;
        }

        if ($y > 90) {
            $y = 90;
        }
        if ($x > 230) {
            $x = 230;
        }

        return '
<?xml version="1.0" encoding="utf-8"?>
<scorestable version="1">
    <styles>
        <style id="LibST_Reset" />
		<style id="LibST_TMWithLegends" />
    </styles>
    <properties>
        <position x="0." y="45." z="-30." />
        <tablesize x="' . $x . '." y="' . $y . '." />
        <taleformat columns="' . $config->tm_score_columns . '" lines="' . $config->tm_score_lines . '"/>
    </properties>
     <columns>
		<column id="LibST_TMBestTime" action="create">
			<width>12.</width>
			<defaultvalue>--:--.---</defaultvalue>
			<textalign>right</textalign>
		</column>
		<column id="LibST_Avatar" action="create">
			<width>6.</width>
			<textalign>center</textalign>
		</column>
		<column id="LibST_Name" action="create">
			<width>32.</width>
			<textalign>left</textalign>
		</column>
		<column id="LibST_ManiaStars" action="create">
			<width>3.</width>
			<textalign>center</textalign>
		</column>
		<column id="LibST_Tools" action="create">
			<width>3.</width>
			<textalign>right</textalign>
		</column>
	 </columns>

    <images>
        <playercard>
            <quad path="file://Media/Manialinks/Trackmania/ScoresTable/playerline-square.dds" />
            <left path="file://Media/Manialinks/Trackmania/ScoresTable/playerline-left.dds" />
            <right path="file://Media/Manialinks/Trackmania/ScoresTable/playerline-right.dds" />
        </playercard>
    </images>
</scorestable>';
    }

}
