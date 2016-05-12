<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Speedometer;

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
class Widgets_Speedometer extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    private $widget;

    public function eXpOnReady()
    {
        $this->enableDedicatedEvents();
        $this->widget = Gui\Widgets\Speedmeter::Create(null);
        $this->widget->setPosition(-14, -74);
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

    public function onBeginMatch()
    {
        $this->widget->show();
    }

    public function eXpOnUnload()
    {
        $this->widget = null;
        Gui\Widgets\Speedmeter::EraseAll();
    }

}
