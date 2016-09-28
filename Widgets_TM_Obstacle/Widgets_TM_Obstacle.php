<?php

/*
 * Copyright (C) 2014
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

namespace ManiaLivePlugins\eXpansion\Widgets_TM_Obstacle;

/**
 * Description of Widgets_CheckpointProgress
 *
 * @author Petri
 */
class Widgets_TM_Obstacle extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    public function eXpOnReady()
    {
        $this->enableDedicatedEvents();
        $this->displayWidget();
    }

    private function displayWidget()
    {
        Gui\Widgets\CpProgress::EraseAll();
        $info = Gui\Widgets\CpProgress::Create(null);
        $info->setSize(70, 60);
        $info->setPosition(55, 0);
        $info->show();
    }

    public function onBeginMap($map, $warmUp, $matchContinuation)
    {
        $this->displayWidget();
    }

    public function onEndMatch($rankings, $winnerTeamOrMap)
    {
        Gui\Widgets\CpProgress::EraseAll();
    }

    public function eXpOnUnload()
    {
        Gui\Widgets\CpProgress::EraseAll();
    }
}
