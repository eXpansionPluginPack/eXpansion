<?php

namespace ManiaLivePlugins\eXpansion\JoinLeaveMessage;

use DateTime;
use Exception;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Helpers\Helper;

class JoinLeaveMessage extends ExpPlugin
{
    private $joinMsg, $joinMsgTime, $leaveMsg, $tabNoticeMsg, $playtimeMsg;

    public function eXpOnLoad()
    {
        $this->enableDedicatedEvents();

        $this->joinMsg = eXpGetMessage('#joinmsg#%5$s #variable#%1$s #joinmsg# (#variable#%2$s#joinmsg#) from #variable#%3$s #joinmsg# joins! #variable#%4$s');
        $this->joinMsgTime = eXpGetMessage('#joinmsg#%5$s #variable#%1$s #joinmsg# (#variable#%2$s#joinmsg#) from #variable#%3$s #joinmsg# joins! #variable#%4$s #joinmsg#Total Playtime: #variable#%6$s');
        $this->leaveMsg = eXpGetMessage('#leavemsg#%4$s #variable#%1$s #leavemsg# (#variable#%2$s#leavemsg#) from #variable#%3$s #leavemsg# leaves! Playtime: #variable#%5$s');
        $this->tabNoticeMsg = eXpGetMessage('#variable#[#error#Info#variable#] #variable#Press TAB to show records widget, use right mouse button for quick menu access.');
        $this->playtimeMsg = eXpGetMessage('#player#This session play time:#variable# %1$s#player#, Total played on server: #variable#%2$s');
    }

    public function eXpOnReady()
    {
        $cmd = $this->registerChatCommand("played", "showPlaytime", 0, true);


        foreach ($this->storage->players as $login => $player) {
            $this->setJoinTime($login);
        }

        foreach ($this->storage->spectators as $login => $player) {
            $this->setJoinTime($login);
        }
    }

    public function setJoinTime($login)
    {
        $this->storage->getPlayerObject($login)->sessionJoinTime = new DateTime();
    }

    public function getSessionTime($login)
    {
        $playtime = "";
        if (!$login) {
            return $playtime;
        }

        $player = $this->storage->getPlayerObject($login);
        $now = new DateTime();

        if (property_exists($player, "sessionJoinTime")) {
            $diff = $now->diff($player->sessionJoinTime, true);
            if ($diff->h) {
                $playtime .= $diff->h . " hours ";
            }
            if ($diff->i) {
                $playtime .= $diff->i . " min ";
            }
            if ($diff->s) {
                $playtime .= $diff->s . " sec ";
            }
        }

        return $playtime;
    }

    public function getTotalPlayTime($login)
    {
        $playtime = "0 hours 0 min 0 sec";
        if (!$login) {
            return $playtime;
        }

        $q = "Select `player_timeplayed` as stamp from `exp_players` WHERE `player_login` = " . $this->db->quote($login) . ";";
        $result = $this->db->execute($q);

        $stamp = intval($result->fetchObject()->stamp);

        if ($stamp) {
            $start = new DateTime();
            $start->setTimestamp(0);

            $time = new DateTime();
            $time->setTimestamp($stamp);

            $diff = $time->diff($start);

            $playtime = $diff->format("%m") . " months " . $diff->format("%d") . " days " . $diff->format("%H") . " hours " . $diff->format("%i") . " min " . $diff->format("%s") . " sec";
        }

        return $playtime;
    }

    public function showPlayTime($login)
    {
        $this->eXpChatSendServerMessage($this->playtimeMsg, $login, array($this->getSessionTime($login), $this->getTotalPlaytime($login)));
    }

    public function onPlayerConnect($login, $isSpectator)
    {
        if (strstr($login, "*fakeplayer")) {
            return;
        }

        try {
            $player = $this->storage->getPlayerObject($login);
            if ($player === null) {
                $msg = "#admin_error#a player with login '#variable#" . $login . "#admin_error#' connected, but no player object for this login exist.";
                AdminGroups::announceToPermission(\ManiaLivePlugins\eXpansion\AdminGroups\Permission::SERVER_ADMIN, "");

                return;
            }
            $this->setJoinTime($login);

            $nick = $player->nickName;
            $country = $this->getCountry($player);

            $spec = "";
            if ($player->isSpectator) {
                $spec = '$n(Spectator)';
            }

            $grpName = AdminGroups::getGroupName($login);

            $config = Config::getInstance();
            $playTime = Helper::formatPastTime($this->expStorage->getDbPlayer($login)->getPlayTime(), 2);
            $joinTimeArgs = array('$z$s' . $nick . '$z$s', $login, $country, $spec, $grpName, $playTime);
            $joinArgs = array('$z$s' . $nick . '$z$s', $login, $country, $spec, $grpName);

            if ($config->hideFromPlayers) {
                $ag = AdminGroups::getInstance();
                if ($config->showTotalPlayOnJoin) {
                    $ag->announceToPermission(\ManiaLivePlugins\eXpansion\AdminGroups\Permission::SERVER_ADMIN, $this->joinMsgTime,
                        $joinTimeArgs);
                } else {
                    $ag->announceToPermission(\ManiaLivePlugins\eXpansion\AdminGroups\Permission::SERVER_ADMIN, $this->joinMsg, $joinArgs);
                }
            } else {
                if ($config->showTotalPlayOnJoin) {
                    $this->eXpChatSendServerMessage($this->joinMsgTime, null, $joinTimeArgs);
                } else {
                    $this->eXpChatSendServerMessage($this->joinMsg, null, $joinArgs);
                }
            }
            // $this->eXpChatSendServerMessage($this->tabNoticeMsg, $login);
        } catch (Exception $e) {
            $this->console($e->getLine() . ":" . $e->getMessage());
        }
    }

    public function onPlayerDisconnect($login, $disconnectionReason = null)
    {
        if (strstr($login, "*fakeplayer")) {
            return;
        }

        $config = Config::getInstance();
        if ($config->showLeaveMessage == false) {
            return;
        }

        try {
            $player = $this->storage->getPlayerObject($login);
            if ($player === null) {
                $msg = "#admin_error#a player with login '#variable#" . $login . "#admin_error#' disconnected, but no player object for this login exist.";
                $ag = AdminGroups::getInstance();
                $ag->announceToPermission(\ManiaLivePlugins\eXpansion\AdminGroups\Permission::SERVER_ADMIN, $msg);

                return;
            }
            $nick = $player->nickName;

            $playtime = $this->getSessionTime($login);

            $grpName = AdminGroups::getGroupName($login);
            $country = $this->getCountry($player);
            if ($config->hideFromPlayers) {
                $ag = AdminGroups::getInstance();
                $ag->announceToPermission(\ManiaLivePlugins\eXpansion\AdminGroups\Permission::SERVER_ADMIN, $this->leaveMsg, array('$z$s' . $nick . '$z$s', $login, $country, $grpName, $playtime));
            } else {
                $this->eXpChatSendServerMessage($this->leaveMsg, null, array('$z$s' . $nick . '$z$s', $login, $country, $grpName, $playtime));
            }

        } catch (Exception $e) {
            $this->console("Error while disconnecting : $login");
        }
    }

    private function getCountry($player)
    {
        $path = str_replace("World|", "", $player->path);
        $country = explode("|", $path);
        if (sizeof($country) > 0) {
            $country = $country[1];
        } else {
            $country = "Unknown";
        }

        return $country;
    }
}