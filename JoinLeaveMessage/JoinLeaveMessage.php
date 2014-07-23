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

	/** @var  Config */
	private $config;

    public function exp_onLoad()
    {
        $this->enableDedicatedEvents();

        $this->joinMsg = exp_getMessage('#player#%5$s #variable#%1$s #player# (#variable#%2$s#player#) from #variable#%3$s #player# joins! #variable#%4$s');
        $this->joinMsgTime = exp_getMessage('#player#%5$s #variable#%1$s #player# (#variable#%2$s#player#) from #variable#%3$s #player# joins! #variable#%4$s Total Playtime: #variable#%6$s');
        $this->leaveMsg = exp_getMessage('#player#%4$s #variable#%1$s #player# (#variable#%2$s#player#) from #variable#%3$s #player# leaves! Playtime: #variable#%5$s');
        $this->tabNoticeMsg = exp_getMessage('#variable#[#error#Info#variable#] #variable#Press TAB to show records widget, use right mouse button for quick menu access.');
        $this->playtimeMsg = exp_getMessage('#player#This session play time:#variable# %1$s#player#, Total played on server: #variable#%2$s');
    }

    public function exp_onReady()
    {
        $cmd = $this->registerChatCommand("played", "showPlaytime", 0, true);


        foreach ($this->storage->players as $login => $player)
            $this->setJoinTime($login);

        foreach ($this->storage->spectators as $login => $player)
            $this->setJoinTime($login);

		$this->config = Config::getInstance();
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
            if ($diff->h)
                $playtime .= $diff->h . " hours ";
            if ($diff->i)
                $playtime .= $diff->i . " min ";
            if ($diff->s)
                $playtime .= $diff->s . " sec ";
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

        $this->exp_chatSendServerMessage($this->playtimeMsg, $login, array($this->getSessionTime($login), $this->getTotalPlaytime($login)));
    }

    public function onPlayerConnect($login, $isSpectator)
    {
        try {
            $player = $this->storage->getPlayerObject($login);
            $this->setJoinTime($login);

            $nick = $player->nickName;
            $country = str_replace("World|", "", $player->path);
            $country = explode("|", $country);
            $country = $country[1];

            $spec = "";
            if ($player->isSpectator)
                $spec = '$n(Spectator)';

            $grpName = AdminGroups::getGroupName($login);

			if($this->config->showTotalPlayOnJoin){
				$playTime = Helper::formatPastTime($this->expStorage->getDbPlayer($login)->getPlayTime()*4, 2);
				echo "Play TIme : $playTime\n";
            	$this->exp_chatSendServerMessage($this->joinMsgTime, null, array($nick, $login, $country, $spec, $grpName, $playTime));
			}else{
				$this->exp_chatSendServerMessage($this->joinMsg, null, array($nick, $login, $country, $spec, $grpName));
			}
            $this->exp_chatSendServerMessage($this->tabNoticeMsg, $login);
        } catch (Exception $e) {
            $this->console($e->getLine() . ":" . $e->getMessage());
        }
    }

    public function onPlayerDisconnect($login, $disconnectionReason = null)
    {
        try {
            $player = $this->storage->getPlayerObject($login);
            $nick = $player->nickName;

            $playtime = $this->getSessionTime($login);

            $grpName = AdminGroups::getGroupName($login);

            $country = str_replace("World|", "", $player->path);
            $country = explode("|", $country);
            $country = $country[1];
            $this->exp_chatSendServerMessage($this->leaveMsg, null, array($nick, $login, $country, $grpName, $playtime));
        } catch (Exception $e) {
            Helper::log("[JoinLeaveMessage]Error while disconnecting : $login");
        }
    }

}
