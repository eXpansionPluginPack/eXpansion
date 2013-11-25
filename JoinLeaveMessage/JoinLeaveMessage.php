<?php

namespace ManiaLivePlugins\eXpansion\JoinLeaveMessage;

class JoinLeaveMessage extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $joinMsg;
    private $leaveMsg;

    public function exp_onLoad() {
	$this->enableDedicatedEvents();

	$this->joinMsg = exp_getMessage('#player#Player %s$1 #player# (#variable#%s$2#player#) from #variable#%s$3 #player# joins! #variable#%s$4');
	$this->leaveMsg = exp_getMessage('#player#Player %s$1 #player# (#variable#%s$2#player#) leaves!');
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
		$spec = __('$n(Spectator)', $login);

	    $this->exp_chatSendServerMessage($this->joinMsg, null, array($nick, $login, $country, $spec));
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
