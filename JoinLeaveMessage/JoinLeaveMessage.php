<?php

namespace ManiaLivePlugins\eXpansion\JoinLeaveMessage;

class JoinLeaveMessage extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $joinMsg;
    private $leaveMsg;
    private $tabNoticeMsg;

    public function exp_onLoad() {
        $this->enableDedicatedEvents();


        $this->joinMsg = exp_getMessage('#player#%5$s #variable#%1$s #player# (#variable#%2$s#player#) from #variable#%3$s #player# joins! #variable#%4$s');
        $this->leaveMsg = exp_getMessage('#player#%4$s #variable#%1$s #player# (#variable#%2$s#player#) leaves!');
        $this->tabNoticeMsg = exp_getMessage('#variable#[#error#Info#variable#] #variable#Some widgets are hidden from the main view, press TAB to show them. Use F8 to disable all custom server graphics.');
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

            $grpName = "Player";
            try {
                $admin = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getAdmin($login);
                if ($admin !== null)
                    $grpName = $admin->getGroup()->getGroupName();
            } catch (\Exception $e) {
                
            }

            $this->exp_chatSendServerMessage($this->joinMsg, null, array($nick, $login, $country, $spec, $grpName));
            $this->exp_chatSendServerMessage($this->tabNoticeMsg, $login);
        } catch (\Exception $e) {
            echo $e->getLine() . ":" . $e->getMessage();
        }
    }

    public function onPlayerDisconnect($login, $disconnectionReason = null) {
        try {
            $player = $this->storage->getPlayerObject($login);
            $nick = $player->nickName;

            $grpName = "Player";
            try {
                $admin = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getAdmin($login);
                if ($admin !== null)
                    $grpName = $admin->getGroup()->getGroupName();
            } catch (\Exception $e) {
                
            }

            $country = str_replace("World|", "", $player->path);
            $this->exp_chatSendServerMessage($this->leaveMsg, null, array($nick, $login, $country, $grpName));
        } catch (\Exception $e) {
            echo $e->getLine() . ":" . $e->getMessage();
        }
    }

}
