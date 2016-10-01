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

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLivePlugins\eXpansion\Gui\Control;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer;

class MapInfoItem extends Control implements ScriptedContainer
{

    protected $quad;
    protected $map;
    protected $author;

    public function __construct($sizeX, $sizeY = 9)
    {

        $this->quad = new Quad($sizeY, $sizeY);
        $this->quad->setAlign("right", "top");
        $this->quad->setPosition($sizeX);
        $this->quad->setId("mapIcon");
        $this->addComponent($this->quad);

        $this->map = new Label($sizeX - $sizeY - 1, 4.5);
        $this->map->setId("mapName");
        $this->map->setPosition($sizeX - $sizeY - 1, -2.25);
        $this->map->setAlign("right", "center");
        $this->map->setTextSize(2);
        $this->addComponent($this->map);


        $this->author = new Label($sizeX - $sizeY - 1, 4.5);
        $this->author->setId("mapAuthor");
        $this->author->setPosition($sizeX - $sizeY - 1, -6.25);
        $this->author->setAlign("right", "center");
        $this->author->setTextSize(1);
        $this->addComponent($this->author);


        $this->setSize($sizeX, $sizeY);
    }

    /**
     * @return Script the script this container needs
     */
    public function getScript()
    {
        $script = new Script("Widgets_TM_topPanel\Gui\Scripts\mapInfo");

        return $script;
    }

    public function setMap(\Maniaplanet\DedicatedServer\Structures\Map $map)
    {
        $this->map->setText(\ManiaLib\Utils\Formatting::stripCodes($map->name, "wosn"));
        $this->author->setText(\ManiaLib\Utils\Formatting::stripCodes($map->author, "wosn"));
    }
}
