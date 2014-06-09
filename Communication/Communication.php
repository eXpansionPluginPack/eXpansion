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

namespace ManiaLivePlugins\eXpansion\Communication;

use ManiaLib\Utils\Formatting;
use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\Communication\Gui\Widgets\CommunicationWidget;
use ManiaLivePlugins\eXpansion\Communication\Gui\Widgets\Messager;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Gui\Windows\PlayerSelection;

/**
 * Description of Communication
 *
 * @author Reaby
 */
class Communication extends ExpPlugin {

    public function exp_onReady() {
	$this->enableDedicatedEvents();

	CommunicationWidget::$action = ActionHandler::getInstance()->createAction(array($this, "guiSendMessage"));
	CommunicationWidget::$selectPlayer = ActionHandler::getInstance()->createAction(array($this, "selectPlayer"));

	$widget = CommunicationWidget::Create();
	$widget->show();

	$this->registerChatCommand("send", "sendPmChat", 2, true);

	foreach ($this->storage->players as $login => $player)
	    $this->onPlayerConnect($login, null);
	foreach ($this->storage->spectators as $login => $player)
	    $this->onPlayerConnect($login, null);
    }

    public function onPlayerConnect($login, $isSpectator) {
	Messager::Erase($login);
	$info = Messager::Create($login);
	$info->clearMessages();
	$info->show();
    }

    public function sendPm($login, $tab, $text) {
	Messager::Erase($login);
	$info = Messager::Create($login);
	$info->sendChat($tab, $text);
	$info->setTimeout(0.5);
	$info->show();
	//echo "pm send;" . $login;
    }

    public function guiSendMessage($login, $entries) {
	//echo "login: '" . $login . "' said:" . $entries['chatEntry'] . "\n";
	//print_r($entries);
	$target = $entries['replyTo'];
	$fromPlayer = $this->storage->getPlayerObject($login);
	$this->sendPm($login, $target, Formatting::stripWideFonts($fromPlayer->nickName) . '$z$fff$s: ' . $entries['chatEntry']);
	$this->sendPm($target, $login, Formatting::stripWideFonts($fromPlayer->nickName) . '$z$fff$s: ' . $entries['chatEntry']);
    }

    public function sendPmChat($login, $target, $text) {
	$fromPlayer = $this->storage->getPlayerObject($login);
	$this->sendPm($login, $target, Formatting::stripWideFonts($fromPlayer->nickName) . '$z$fff$s: ' . $text);
	$this->sendPm($target, $login, Formatting::stripWideFonts($fromPlayer->nickName) . '$z$fff$s: ' . $text);
    }

    public function selectPlayer($login) {
	$window = PlayerSelection::Create($login);
	$window->setController($this);
	$window->setTitle('Select Player');
	$window->setSize(85, 100);
	$window->populateList(array($this, 'openNewTab'), 'Select');
	$window->centerOnScreen();
	$window->show();
    }

    public function openNewTab($login, $target) {
	PlayerSelection::Erase($login);

	$info = Messager::Create($login);
	$info->openNewTab($target);
	//$info->setTimeout(0.5);
	$info->show();
    }

    public function exp_onUnload() {
	Messager::EraseAll();
	CommunicationWidget::EraseAll();
	parent::exp_onUnload();
    }

}
