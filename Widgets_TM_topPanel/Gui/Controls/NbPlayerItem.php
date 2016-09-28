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

use ManiaLib\Gui\Elements\Quad;
use ManiaLive\Data\Storage;
use ManiaLivePlugins\eXpansion\Gui\Control;
use ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer;
use ManiaLivePlugins\eXpansion\Helpers\Maniascript;

class NbPlayerItem extends Control implements ScriptedContainer
{

    protected $players_bg_quad, $players_quad, $spec_bg_quad, $spec_quad;

    protected $lbl_players, $lbl_specs, $lbl_info, $lbl_title;

    protected $div;

    public function __construct($sizeX, $sizeY = 9)
    {
        $div = $sizeX / 3;
        $this->div = $div;

        $labelProto = new DicoLabel($div * 2, 4.3);
        $labelProto->setTextSize(1);
        $labelProto->setPosition(($div * 2 / 2), -2.25);
        $labelProto->setAlign("center", "center");
        $labelProto->setTextColor("000");

        $quadProto = new Quad($div * 2, 4.3);
        $quadProto->setAlign("left", "center");
        $quadProto->setPosition(0, -2.25);
        $quadProto->setBgcolor("FFFA");

        $this->players_bg_quad = clone $quadProto;
        $this->players_quad = clone $quadProto;
        $this->players_quad->setId("playerQ");

        $this->spec_bg_quad = clone $quadProto;
        $this->spec_bg_quad->setPosY(-6.5);

        $this->spec_quad = clone $quadProto;
        $this->spec_quad->setPosY(-6.5);
        $this->spec_quad->setId("specQ");


        $this->lbl_players = clone $labelProto;
        $this->lbl_players->setText("Players");

        $this->lbl_specs = clone $labelProto;
        $this->lbl_specs->setPosY(-6.45);
        $this->lbl_specs->setText("Spectators");

        $infoProto = new DicoLabel($div * 2, 4.5);
        $infoProto->setTextSize(1);
        $infoProto->setPosition(($div * 3) - ($div / 2), -2.25);
        $infoProto->setAlign("center", "center");
        $infoProto->setTextColor("fff");
        $infoProto->setStyle("TextRaceChrono");

        $this->lbl_title = clone $infoProto;
        $this->lbl_title->setId("players");
        $this->lbl_title->setText("1/16");

        $this->lbl_info = clone $infoProto;
        $this->lbl_info->setPosY(-6.45);
        $this->lbl_info->setId("specs");
        $this->lbl_info->setText("0/16");

        $this->addComponent($this->players_bg_quad);
        $this->addComponent($this->players_quad);
        $this->addComponent($this->spec_bg_quad);
        $this->addComponent($this->spec_quad);

        $this->addComponent($this->lbl_players);
        $this->addComponent($this->lbl_specs);
        $this->addComponent($this->lbl_info);
        $this->addComponent($this->lbl_title);
        $this->setSize($sizeX, $sizeY);
    }

    /**
     * @return Script the script this container needs
     */
    public function getScript()
    {
        /** @var Storage $storage */
        $storage = Storage::getInstance();
        $script = new Script("Widgets_TM_topPanel\\Gui\\Scripts\\nbPlayer");
        $script->setParam('maxPlayers', $storage->server->currentMaxPlayers);
        $script->setParam('maxSpecs', $storage->server->currentMaxSpectators);
        $script->setParam('div', Maniascript::getReal($this->div * 2));

        return $script;
    }
}
