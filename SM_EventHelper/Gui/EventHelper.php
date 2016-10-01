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

namespace ManiaLivePlugins\eXpansion\SM_EventHelper\Gui;

use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget;

/**
 * Description of EventHelper
 *
 * @author Reaby
 */
class EventHelper extends PlainWidget
{
    /**
     * @var Script
     */
    private $script;

    public static $actions = array();

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->setName("EventHelper");
        $this->setPosition(0, 900);
        $entry = new \ManiaLib\Gui\Elements\Entry();
        $entry->setPosition(0, 0);
        $entry->setId("timeOrScore");
        $entry->setName("timeOrScore");
        $this->addComponent($entry);


        $entry = new \ManiaLib\Gui\Elements\Entry();
        $entry->setPosition(0, -6);
        $entry->setId("index");
        $entry->setName("index");
        $this->addComponent($entry);

        $this->script = new Script("SM_EventHelper\Gui\Script");
        $this->script->setParam("cpAction", self::$actions['checkpoint']);
        $this->script->setParam("finishAction", self::$actions['finish']);
        $this->registerScript($this->script);
    }

    protected function onDraw()
    {
        $storage = \ManiaLive\Data\Storage::getInstance();
        $this->script->setParam("cpCount", $storage->currentMap->nbCheckpoints);
        parent::onDraw();
    }
}
