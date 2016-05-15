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

namespace ManiaLivePlugins\eXpansion\KnockOut;

use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj;
use ManiaLivePlugins\eXpansion\KnockOut\Structures\KOplayer;
use Maniaplanet\DedicatedServer\Structures\GameInfos;
use Phine\Exception\Exception;

/**
 * Description of KnockOut
 *
 * @author Reaby
 */
class KnockOut extends ExpPlugin
{

    /** @var KOplayer[] */
    private $players = array();

    private $playersAtStart = 0;

    private $round = 0;

    private $isRunning = false;

    private $isWarmup = false;

    private $delay = false;

    private $adm_ko = null;

    private $msg_newRound, $msg_koStart, $msg_knockout, $msg_knockoutDNF, $msg_champ;

    public function eXpOnLoad()
    {
        $this->msg_newRound = exp_getMessage('#ko#KnockOut! Round: #variable#%1$s #ko#Players #variable#%2$s #ko#/#variable#%3$s remain');
        $this->msg_koStart = exp_getMessage('#ko#KnockOut #variable#starts #ko#after next map');
        $this->msg_koStop = exp_getMessage('#ko#KnockOut has been #variable#stopped.');
        $this->msg_knockout = exp_getMessage('#ko#KnockOut! #variable# %1$s $z$s#ko# knocked out, but the game is still on!');
        $this->msg_knockoutDNF = exp_getMessage('#ko#KnockOut! #variable# %1$s $z$s#ko# knocked out, since no finish!');
        $this->msg_champ = exp_getMessage('#ko#KnockOut! #variable# %1$s $z$s#ko# is the CHAMP!!! congrats');
    }

    public function eXpOnReady()
    {
        $this->enableDedicatedEvents();

        $adminGroups = AdminGroups::getInstance();

        $this->adm_ko = AdminGroups::addAdminCommand('ko', $this, 'chatCommands', Permission::GAME_SETTINGS);
        $this->adm_ko->setHelp('/ko start, stop, res, skip');
        $adminGroups->addShortAlias($this->adm_ko, 'ko');

        $this->colorParser->registerCode("ko", Config::getInstance(), "koColor");
    }

    public function chatCommands($login, $params = array())
    {
        try {
            $command = array_shift($params);
            switch (strtolower($command)) {
                case "start":
                    $this->koStart();
                    break;
                case "stop":
                    $this->koStop();
                    break;
                case "res":
                    $this->delay = true;
                    $this->connection->restartMap();
                    break;
                case "skip":
                    $this->delay = true;
                    $this->connection->nextMap();
                    break;

                default:
                    $this->eXpChatSendServerMessage("Possible values: start, stop, res, skip", $login);
                    break;
            }
        } catch (Exception $e) {
            $this->eXpChatSendServerMessage('#admin_error#Error:' . $e->getMessage(), $fromLogin);
        }
    }

    /**
     * creates array of KOplayers from players not specating at the moment.
     *
     * @return KOplayer[]
     */
    public function getNewPlayers()
    {
        $outPlayers = array();
        foreach ($this->storage->players as $login => $player) {
            $outPlayers[$login] = new KOplayer($player);
        }

        return $outPlayers;
    }

    public function getNewPlayer($login)
    {
        $player = $this->storage->getPlayerObject($login);

        return new KOplayer($player);
    }

    /**
     * Starts the KnockOut
     *
     * @param string $login
     */
    public function koStart()
    {
        $this->reset();
        $this->isRunning = true;
        $this->delay = true;
        $this->players = $this->getNewPlayers();
        $this->playersAtStart = count($this->players);
        $this->eXpChatSendServerMessage($this->msg_koStart, null);
    }

    public function reset()
    {
        $this->players = array();
        $this->playersAtStart = 0;

        $this->round = 0;
        $this->isRunning = false;
        $this->isWarmup = false;
        $this->delay = false;
    }

    /**
     * Stops
     *
     * @param string $login
     */
    public function koStop()
    {
        $this->reset();
        $this->isRunning = false;
        $this->eXpChatSendServerMessage($this->msg_koStop, null);

// release spectators to play
        foreach ($this->storage->spectators as $player) {
            $this->connection->forceSpectator($player->login, 2);
            $this->connection->forceSpectator($player->login, 0);
        }
    }

    public function onBeginMatch()
    {
        $this->isWarmup = $this->connection->getWarmUp();
        if ($this->isRunning && !$this->isWarmup) {
            $this->round++;
            $this->eXpChatSendServerMessage($this->msg_newRound, null, array("" . $this->round, "" . count($this->players), "" . $this->playersAtStart));
            $this->delay = false;
        }
    }

    public function onBeginRound()
    {
        $this->isWarmup = $this->connection->getWarmUp();
        $this->delay = false;
    }

    public function sortAsc(&$array)
    {
        if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_TIMEATTACK) {
            \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::asortAsc($array, "bestTime");
        } else {
            \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::asortAsc($array, "score");
        }
    }

    public function sortDesc(&$array)
    {
        if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_TIMEATTACK) {
            \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::asortDesc($array, "bestTime");
        } else {
            \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::asortDesc($array, "score");
        }
    }

    public function onEndMatch($rankings, $winnerTeamOrMap)
    {

        if ($this->delay || !$this->isRunning || $this->isWarmup)
            return;

        $ranking = $this->connection->getCurrentRanking(-1, 0);
        $this->sortAsc($ranking);
        $nbKo = 1;

        $knockedOut = 0;

        $dnf = $this->findDNF($ranking);

        if (count($dnf) > 0) {
            $out = array();
            foreach ($dnf as $login => $player) {
                if (array_key_exists($login, $this->players)) {
                    $out[] = $player->nickName;
                    unset($this->players[$login]);
                    $knockedOut++;
                    $this->connection->forceSpectator($login, 1);
                }
            }
            $this->eXpChatSendServerMessage($this->msg_knockoutDNF, null, array(implode('$z$s, ', $out)));
        }


        $this->sortDesc($ranking);
        $out = array();
        $prop = 'score';
        if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_TIMEATTACK) {
            $prop = "bestTime";
        }

        print_r($ranking);

        foreach ($ranking as $player) {
            if ($player->{$prop} <= 0)
                continue;
            if ($knockedOut < $nbKo) {
                if (array_key_exists($player->login, $this->players)) {
                    $out[] = $player->nickName;
                    unset($this->players[$player->login]);
                    $knockedOut++;
                }
            }
        }
        if (count($out) > 0) {
            $this->eXpChatSendServerMessage($this->msg_knockout, null, array(implode('$z$s', $out)));
        }

        if (count($this->players) == 1) {
            reset($this->players);
            $player = current($this->players);
            $this->eXpChatSendServerMessage($this->msg_champ, null, array($player->nickName));
        }

        if (count($this->players) >= 1) {
            $this->koStop();
        }
    }

    /**
     *
     * @param \Maniaplanet\DedicatedServer\Structures\PlayerRanking[] $array
     */
    public function findDNF($array)
    {
        $outArray = array();

        $prop = 'score';
        if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_TIMEATTACK) {
            $prop = "bestTime";
        }

        foreach ($array as $player) {
            if (array_key_exists($player->login, $this->players)) {
                if ($player->{$prop} <= 0) {
                    $outArray[$player->login] = $player;
                }
            }
        }

        return $outArray;
    }

    public function eXpOnUnload()
    {
        AdminGroups::removeShortAllias("ko");
        AdminGroups::removeAdminCommand($this->adm_ko);

        parent::eXpOnUnload();
    }

}
