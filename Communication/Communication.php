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
	Gui\Widgets\CommunicationWidget::$action = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, "sendMessage"));

	$widget = Gui\Widgets\CommunicationWidget::Create();
	$widget->show();
	$this->registerChatCommand("send", "sendPm", 2, true);
    }

    public function sendPm($login, $tab, $text) {
	$info = Gui\Widgets\Messager::Create($login);
	$info->sendChat($tab, $text);
	$info->show();
	//echo "pm send;" . $login;
    }

    public function sendMessage($login, $entries) {
	//echo "login: '" . $login . "' said:" . $entries['chatEntry'] . "\n";
	//print_r($entries);
	$target = $entries['replyTo'];


	$fromPlayer = $this->storage->getPlayerObject($login);
	// $toPlayer = $this->storage->getPlayerObject($target);

	$this->sendPm($login, $target, \ManiaLib\Utils\Formatting::stripWideFonts($fromPlayer->nickName) . '$z$fff$s: ' . $entries['chatEntry']);
	$this->sendPm($target, $login, \ManiaLib\Utils\Formatting::stripWideFonts($fromPlayer->nickName) . '$z$fff$s: ' . $entries['chatEntry']);
    }

}
