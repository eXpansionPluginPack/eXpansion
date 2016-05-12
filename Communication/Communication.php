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
class Communication extends ExpPlugin
{

    private $lastCheck = 0;

    /** @var \Maniaplanet\DedicatedServer\Structures\Player */
    private $cachedIgnoreList = array();

    public function eXpOnReady()
    {
        $this->enableDedicatedEvents();

        CommunicationWidget::$action = ActionHandler::getInstance()->createAction(array($this, "guiSendMessage"));
        CommunicationWidget::$selectPlayer = ActionHandler::getInstance()->createAction(array($this, "selectPlayer"));

        $widget = CommunicationWidget::Create();
        $widget->show();

        $this->registerChatCommand("send", "sendPmChat", -1, true);

        foreach ($this->storage->players as $login => $player)
            $this->onPlayerConnect($login, null);
        foreach ($this->storage->spectators as $login => $player)
            $this->onPlayerConnect($login, null);

        $this->lastCheck = time();
        $this->cachedIgnoreList = $this->connection->getIgnoreList(-1, 0);
    }

    public function onPlayerConnect($login, $isSpectator)
    {
        Messager::Erase($login);
        $info = Messager::Create($login);
        $info->clearMessages();
        $info->setTimeout(0.5);
        $info->show();
    }

    public function send($login, $tab, $text)
    {
        $login = str_replace('–', '-', $login); // undo replacing maniascript en hyphen to normal one, so message reaches the right person...
        Messager::Erase($login);
        $info = Messager::Create($login);
        $info->sendChat($tab, $text);
        $info->setTimeout(0.5);
        $info->show();
    }

    public function sendPm($login, $target, $text)
    {
        if (!$this->checkPlayer($login)) {
            $this->send($login, $target, '$d00' . __("You are being ignored. Message not sent.", $login));

            return;
        }

        /* if (!$this->checkPlayer($target)) {
          $this->send($login, $target, '$f00' . __("You can't send a message to " . $target . ", he is ignored or disconnected.", $login));
          return;
          } */

        $fromPlayer = $this->storage->getPlayerObject($login);
        $this->send($login, $target, '$z$fffMe: ' . $text);
        $this->send($target, $login, '$z$222' . Formatting::stripWideFonts($fromPlayer->nickName) . '$z$222: ' . $text);
    }

    /**
     * checks if player is found at server
     *
     * @param string $login
     *
     * @return boolean
     */
    private function checkPlayer($login)
    {
        // sync ignorelist every 10 seconds...
        if (time() > $this->lastCheck + 10) {
            $this->lastCheck = time();
            $this->cachedIgnoreList = $this->connection->getIgnoreList(-1, 0);
        }

        foreach ($this->cachedIgnoreList as $player) {
            if ($player->login == $login) {
                return false;
            }
        }

        $test = $this->storage->getPlayerObject($login);
        if (empty($test)) {
            return false;
        }

        return true;
    }

    public function guiSendMessage($login, $entries)
    {
        $target = $entries['replyTo'];

        $this->sendPm($login, $target, $entries['chatEntry']);
    }

    public function sendPmChat($login, $params = false)
    {
        if ($params === false) {
            $this->eXpChatSendServerMessage($this->msg_help, $login);

            return;
        }
        $text = explode(" ", $params);
        $target = array_shift($text);
        $text = implode(" ", $text);

        $this->sendPm($login, $target, $text);
    }

    public function selectPlayer($login)
    {
        $window = PlayerSelection::Create($login);
        $window->setController($this);
        $window->setTitle('Select Player');
        $window->setSize(85, 100);
        $window->populateList(array($this, 'openNewTab'), 'Select');
        $window->centerOnScreen();
        $window->show();
    }

    public function openNewTab($login, $target)
    {
        PlayerSelection::Erase($login);

        $info = Messager::Create($login);
        $info->openNewTab($target);
        $info->setTimeout(0.5);
        $info->show();
    }

    public function eXpOnUnload()
    {
        Messager::EraseAll();
        CommunicationWidget::EraseAll();
        /** @var ActionHandler $actionH */
        $actionH = ActionHandler::getInstance();
        $actionH->deleteAction(CommunicationWidget::$action);
        $actionH->deleteAction(CommunicationWidget::$selectPlayer);
        parent::eXpOnUnload();
    }

}
