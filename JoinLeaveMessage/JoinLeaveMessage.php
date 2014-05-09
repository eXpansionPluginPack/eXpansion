<?php

namespace ManiaLivePlugins\eXpansion\JoinLeaveMessage;

class JoinLeaveMessage extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $joinMsg;
    private $leaveMsg;
    private $tabNoticeMsg;

    public function exp_onLoad() {
	$this->enableDedicatedEvents();
	
	$this->joinMsg = exp_getMessage('#player#%5$s #variable#%1$s #player# (#variable#%2$s#player#) from #variable#%3$s #player# joins! #variable#%4$s');
	$this->leaveMsg = exp_getMessage('#player#%4$s #variable#%1$s #player# (#variable#%2$s#player#) from #variable#%3$s #player# leaves! Playtime: #variable#%5$s');
	$this->tabNoticeMsg = exp_getMessage('#variable#[#error#Info#variable#] #variable#Press TAB to show records widget, use right mouse button for quick menu access.');
    }

    public function exp_unload() {
	parent::exp_unload();
    }

    public function onPlayerConnect($login, $isSpectator) {
	try {
	    $player = $this->storage->getPlayerObject($login);
	    $player->joinedServer = time();
	    $nick = $player->nickName;
	    $country = str_replace("World|", "", $player->path);
	    $country = explode("|", $country);
	    $country = $country[1];

	    $spec = "";
	    if ($player->isSpectator)
		$spec = __("\$n(Spectator)", $login);

	    $grpName = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getGroupName($login);
	    $this->exp_chatSendServerMessage($this->joinMsg, null, array($nick, $login, $country, $spec, $grpName));
	    $this->exp_chatSendServerMessage($this->tabNoticeMsg, $login);
	} catch (\Exception $e) {
	    $this->console($e->getLine() . ":" . $e->getMessage());
	}
    }

    public function onPlayerDisconnect($login, $disconnectionReason = null) {
	try {
	    $player = $this->storage->getPlayerObject($login);
	    $playtime = date("H:i.s", (time() - $player->joinedServer));
	    $nick = $player->nickName;

	    $grpName = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getGroupName($login);
	    
	    $country = str_replace("World|", "", $player->path);
	    $country = explode("|", $country);
	    $country = $country[1];
	    $this->exp_chatSendServerMessage($this->leaveMsg, null, array($nick, $login, $country, $grpName, $playtime));
	} catch (\Exception $e) {
	    echo $e->getLine() . ":" . $e->getMessage();
	}
    }

}
