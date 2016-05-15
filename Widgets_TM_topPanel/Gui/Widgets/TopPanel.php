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

namespace ManiaLivePlugins\eXpansion\Widgets_TM_topPanel\Gui\Widgets;

/**
 * Description of TopPanel
 *
 * @author Reaby
 */
class TopPanel extends \ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget
{

    protected $bg, $logo;

    protected $frameRight, $frameLeft, $frameCenter;

    protected $script;

    /** @var \ManiaLive\Data\Storage */
    protected $storage;

    protected $divBase = 12;

    private function getBaseSize()
    {
        return (320 / $this->divBase);
    }

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->setPosition(0, 90);
        $this->setAlign("center", "top");

        $this->setName("Top Panel");
        $this->storage = \ManiaLive\Data\Storage::getInstance();
        $this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(320, 9);
        $this->bg->setAlign("center");
        $this->addComponent($this->bg);

        $config = \ManiaLivePlugins\eXpansion\Gui\Config::getInstance();

        $this->frameCenter = new \ManiaLive\Gui\Controls\Frame(0, 0);
        $this->addComponent($this->frameCenter);

        $line = new \ManiaLib\Gui\Layouts\Line();
        $line->setMargin(1, 1);

        $this->frameLeft = new \ManiaLive\Gui\Controls\Frame(-159, 0);
        $this->frameLeft->setLayout($line);
        $this->addComponent($this->frameLeft);

        $line = new \ManiaLib\Gui\Layouts\Line();
        $line->setMargin(1, 1);
        $line->setAlign("right");

        $offset = 0;
        $this->frameRight = new \ManiaLive\Gui\Controls\Frame();
        $this->frameRight->setAlign("right");
        $this->frameRight->setLayout($line);
        $this->addComponent($this->frameRight);

        $z = $this->getBaseSize();

        /*		 * **********************************
         * Left components
         * ********************************** */
        $this->frameLeft->addComponent($this->getClock(2 * $z));
        $this->frameLeft->addComponent($this->getPlayerInfo(2 * $z));

        /*		 * *********************************
         * Center components
         * ********************************** */
        $this->frameCenter->addComponent($this->getServerNameItem(4 * $z));


        /*		 * **********************************
         * Right components
         * ********************************** */
        $this->frameRight->addComponent($this->getMapInfo(2 * $z));

        $this->frameRight->setPosX(160 - (2 * $z));
    }

    protected function getServerNameItem($size)
    {
        $item = new \ManiaLivePlugins\eXpansion\Widgets_TM_topPanel\Gui\Controls\ServerInfoItem($size);
        $item->setText("Ladder limits " . ($this->storage->server->ladderServerLimitMin / 1000) . " - " . ($this->storage->server->ladderServerLimitMax / 1000) . "k");

        return $item;
    }

    protected function getMapInfo($size)
    {
        $item = new \ManiaLivePlugins\eXpansion\Widgets_TM_topPanel\Gui\Controls\MapInfoItem($size);
        $item->setMap($this->storage->currentMap);

        return $item;
    }

    protected function getPlayerInfo($sizeX)
    {
        $item = new \ManiaLivePlugins\eXpansion\Widgets_TM_topPanel\Gui\Controls\NbPlayerItem($sizeX);

        return $item;
    }

    protected function getClock($sizeX)
    {
        $item = new \ManiaLivePlugins\eXpansion\Widgets_TM_topPanel\Gui\Controls\ClockItem($sizeX);

        return $item;
    }

}
