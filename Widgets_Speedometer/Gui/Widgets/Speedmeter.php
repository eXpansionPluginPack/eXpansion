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

namespace ManiaLivePlugins\eXpansion\Widgets_Speedometer\Gui\Widgets;

/**
 * Description of Speedmeter
 *
 * @author Reaby
 */
class Speedmeter extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{

    function eXpOnBeginConstruct()
    {
        $this->setName("Speed'o'meter");

        $label = new \ManiaLib\Gui\Elements\Label(20, 6);
        $label->setTextColor("fff");
        $label->setTextSize(2);
        $label->setId("speed");
        $label->setText("");
        $label->setAlign("center", "top");
        $label->setPosition(0, -6);
        $this->addComponent($label);

        $gauge = new \ManiaLivePlugins\eXpansion\Gui\Elements\Gauge(30, 8);
        $gauge->setStyle(\ManiaLivePlugins\eXpansion\Gui\Elements\Gauge::EnergyBar);
        $gauge->setGrading(0);
        $gauge->setId("bar");
        $gauge->setColorize("3af");
        $this->addComponent($gauge);

        $script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Widgets_Speedometer\Gui\Script");
        $this->registerScript($script);
    }
}
