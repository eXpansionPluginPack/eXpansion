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

/**
 * Description of Communication
 *
 * @author Reaby
 */
class Communication extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    public function exp_onReady() {
	$this->enableDedicatedEvents();

	Gui\Widgets\CommunicationWidget::$action = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, "guiSendMessage"));

	$widget = Gui\Widgets\CommunicationWidget::Create();
	$widget->show();
	$this->registerChatCommand("send", "sendPm", 2, true);
    }

    public function onPlayerConnect($login, $isSpectator) {
	$info = Gui\Widgets\Messager::Create($login);
	$info->clearMessages();
	$info->setTimeout(0.5);
	$info->show();
    }

    public function sendPm($login, $tab, $text) {
	$fromPlayer = $this->storage->getPlayerObject($login);

	$info = Gui\Widgets\Messager::Create($login);
	$info->sendChat($tab, \ManiaLib\Utils\Formatting::stripWideFonts($fromPlayer->nickName) . '$z$fff$s: ' . $text);
	$info->setTimeout(0.5);
	$info->show();
	//echo "pm send;" . $login;
    }

    public function guiSendMessage($login, $entries) {
	//echo "login: '" . $login . "' said:" . $entries['chatEntry'] . "\n";
	//print_r($entries);
	$target = $entries['replyTo'];
	$this->sendPm($login, $target, $entries['chatEntry']);
	$this->sendPm($target, $login, $entries['chatEntry']);
    }

    public function exp_onUnload() {
	Gui\Widgets\Messager::EraseAll();
	Gui\Widgets\CommunicationWidget::EraseAll();
	parent::exp_onUnload();
    }

}
