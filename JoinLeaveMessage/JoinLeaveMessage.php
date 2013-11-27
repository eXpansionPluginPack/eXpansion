<?php

namespace ManiaLivePlugins\eXpansion\JoinLeaveMessage;

class JoinLeaveMessage extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $joinMsg;
    private $leaveMsg;
    private $tabNoticeMsg;

    public function exp_onLoad() {
	$this->enableDedicatedEvents();


	$this->joinMsg = exp_getMessage('#player#Player %s$1 #player# (#variable#%s$2#player#) from #variable#%s$3 #player# joins! #variable#%s$4');
	$this->leaveMsg = exp_getMessage('#player#Player %s$1 #player# (#variable#%s$2#player#) leaves!');
	$this->tabNoticeMsg = exp_getMessage('#variable#[Info] Some widgets are hidden from the main view, press TAB to show them. Use F8 to disable all custom server graphics.');
    }

    public function exp_unload() {
	parent::exp_unload();
    }

    public function onPlayerConnect($login, $isSpectator) {
	try {
	    $player = $this->storage->getPlayerObject($login);

	    $nick = $player->nickName;
	    $country = str_replace("World|", "", $player->path);

	    $spec = "";
	    if ($player->isSpectator)
		$spec = __("\$n(Spectator)", $login);

	    $this->exp_chatSendServerMessage($this->joinMsg, null, array($nick, $login, $country, $spec));
	    $this->exp_chatSendServerMessage($this->tabNoticeMsg, $login);
	} catch (\Exception $e) {
	    echo $e->getLine() . ":" . $e->getMessage();
	}
    }

    public function onPlayerDisconnect($login, $disconnectionReason = null) {
	try {
	    $player = $this->storage->getPlayerObject($login);
	    $nick = $player->nickName;
	    $country = str_replace("World|", "", $player->path);
	    $this->exp_chatSendServerMessage($this->leaveMsg, null, array($nick, $login, $country));
	} catch (\Exception $e) {
	    echo $e->getLine() . ":" . $e->getMessage();
	}
    }

}
