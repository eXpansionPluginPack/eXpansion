<?php

/*
 * Copyright (C) 2014
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

namespace ManiaLivePlugins\eXpansion\Widgets_Livecp;

use ManiaLive\Data\Player;
use ManiaLivePlugins\eXpansion\Widgets_Livecp\Gui\Widgets\CpProgress;
use ManiaLivePlugins\eXpansion\Widgets_Livecp\Structures\CpInfo;

/**
 * Description of Widgets_Livecp
 *
 * @author Petri
 */
class Widgets_Livecp extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    /** @var CpInfo[] */
    private $players = array();
    /** @var bool */
    private $update = false;
    /** @var int */
    private $lastSend = 0;

    public function eXpOnReady()
    {
        $this->enableDedicatedEvents();
        $this->enableStorageEvents();
        $this->enableTickerEvent();
        $this->reset();
        $this->lastSend = time() - 3;
        $this->displayWidget();
    }


    public function onTick()
    {

        if ($this->update && $this->lastSend <= time() - 2) {
            $this->update = false;
            CpProgress::EraseAll();
            $info = CpProgress::Create(null);
            $info->setData($this->players);
            $info->setPosition(-160, 60);
            $info->show();
            $this->lastsend = time();

        }
    }


    private function displayWidget()
    {
        $this->update = true;
    }

    public function onPlayerCheckpoint($playerUid, $login, $timeOrScore, $curLap, $checkpointIndex)
    {
        $this->players[$login] = new CpInfo($checkpointIndex, $timeOrScore);
        $this->displayWidget();
    }

    public function onPlayerConnect($login, $isSpectator)
    {
        if ($isSpectator) {
            return;
        }
        $this->players[$login] = new CpInfo();
        $this->displayWidget();
    }

    public function onPlayerDisconnect($login, $disconnectionReason)
    {
        if (isset($this->players[$login])) {
            unset($this->players[$login]);
        }
        $this->displayWidget();
    }

    public function onPlayerFinish($playerUid, $login, $timeOrScore)
    {
        if ($timeOrScore == 0) {
            if ($this->players[$login]->cpIndex != $this->storage->currentMap->nbCheckpoints - 1) {
                $this->players[$login] = new CpInfo();
            }
        } else {
            $this->players[$login] = new CpInfo($this->storage->currentMap->nbCheckpoints - 1, $timeOrScore);
        }

        $this->displayWidget();
    }

    public function onPlayerChangeSide($playerInfo, $old)
    {
        $player = Player::fromArray($playerInfo);
        $login = $player->login;

        if ($player->spectator) {
            if (isset($this->players[$login])) {
                unset($this->players[$login]);
            }
        } else {
            $this->players[$login] = new CpInfo();
        }

        $this->displayWidget();
    }

    public function onBeginMap($map, $warmUp, $matchContinuation)
    {
        $this->reset();
        $this->displayWidget();
    }

    public function onEndMatch($rankings, $winnerTeamOrMap)
    {
        cpProgress::EraseAll();
    }

    public function eXpOnUnload()
    {
        CpProgress::EraseAll();
        $this->disableDedicatedEvents();
        $this->disableStorageEvents();
        $this->disableTickerEvent();
    }

    public function reset()
    {
        $this->players = array();
        foreach ($this->storage->players as $player) {
            $this->players[$player->login] = new CpInfo();
        }
    }
}
