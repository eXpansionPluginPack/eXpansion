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

namespace ManiaLivePlugins\eXpansion\Communication\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Gui;

/**
 * Description of CommunicationWidget
 *
 * @author Reaby
 */
class Messager extends \ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget {

    private $script;

    protected function onConstruct() {
	parent::onConstruct();
	$this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Communication\Gui\Messager");
	$this->registerScript($this->script);
    }

    public function sendChat($tab, $text) {
	$this->script->setParam("action", "sendMessage");
	$this->script->setParam("tab", Gui::fixString($tab));
	$this->script->setParam("text", Gui::fixString($text));
    }

    public function clearMessages() {
	$this->script->setParam("action", "clearMessages");
	$this->script->setParam("tab", "");
	$this->script->setParam("text", "");
    }

    public function closeTab($tab) {
	$this->script->setParam("action", "closeTab");
	$this->script->setParam("tab", Gui::fixString($tab));
	$this->script->setParam("text", "");
    }

    public function openNewTab($tab) {
	$this->script->setParam("action", "openTab");
	$this->script->setParam("tab", Gui::fixString($tab));
	$this->script->setParam("text", "");
    }

}
