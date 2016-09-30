<?php

/**
 * @author       Oliver de Cramer (oliverde8 at gmail.com)
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

class ServerInfoItem extends \ManiaLivePlugins\eXpansion\Gui\Control implements ScriptedContainer
{

    protected $lbl_value;
    protected $lbl_title;

    public function __construct($sizeX, $sizeY = 9)
    {
        $this->lbl_title = new \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel($sizeX, 4 - 5);
        $this->lbl_title->setPosition(0, -6.25);
        $this->lbl_title->setTextSize(1);
        $this->lbl_title->setAlign("center", "center");
        $this->addComponent($this->lbl_title);


        $this->lbl_value = new \ManiaLib\Gui\Elements\Label($sizeX, 4.5);
        $this->lbl_value->setStyle("TextRaceMessageBig");
        $this->lbl_value->setTextSize(3);
        $this->lbl_value->setPosition(0, -2.25);
        $this->lbl_value->setAlign("center", "center");
        $this->lbl_value->setId("serverName");
        $this->addComponent($this->lbl_value);

        $this->setSize($sizeX, $sizeY);

    }

    public function setText($title)
    {
        $this->lbl_title->setText($title);
    }

    /**
     * @return Script the script this container needs
     */
    public function getScript()
    {
        $script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Widgets_TM_topPanel\Gui\Scripts\serverInfo");

        return $script;
    }
}
