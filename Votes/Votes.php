<?php

namespace ManiaLivePlugins\eXpansion\Votes;

use ManiaLive\Event\Dispatcher;
use ManiaLive\Utilities\Console;
use ManiaLivePlugins\eXpansion\Core\i18n\Message;
use \ManiaLivePlugins\eXpansion\Votes\Config;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;

class Votes extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $config;
    private $useQueue = false;
    private $timer = 0;
    private $defTimeOut;
    private $voter = null;
    private $debug = true;

    function exp_onInit() {

        $this->config = Config::getInstance();

        //Oliverde8 Menu
        if ($this->isPluginLoaded('oliverde8\HudMenu'))
            Dispatcher::register(\ManiaLivePlugins\oliverde8\HudMenu\onOliverde8HudMenuReady::getClass(), $this);
    }

    public function exp_onLoad() {
        parent::exp_onLoad();

        $this->enableStorageEvents();
        $this->enableDedicatedEvents();

        if ($this->config->restartVote_enable) {
            $this->registerChatCommand("restartmap", "vote_Restart", 0, true);
        }
        if ($this->config->skipVote_enable) {
            $this->registerChatCommand("skipmap", "vote_Skip", 0, true);
        }
        //$cmd = $this->addAdminCommand("poll", $this, "vote_Poll", null);
        //$cmd->setHelp("Run a yes/no vote with the entered question");

        $cmd = AdminGroups::addAdminCommand('cancelvote', $this, 'cancelVote', 'cancel_vote');
        $cmd->setHelp('Cancel current running callvote');
        AdminGroups::addAlias($cmd, "cancel");
    }

    public function exp_onReady() {
        parent::exp_onReady();

        $this->timer = time();

        if ($this->isPluginLoaded('eXpansion\Maps') && $this->config->restartVote_useQueue) {
            $this->useQueue = true;
            if ($this->debug) echo "[exp\Votes] Restart votes set to queue\n";
        } else {
            if ($this->debug) echo "[exp\Votes] Restart vote set to normal\n";
        }

        /*if ($this->config->defaultVotes_disable) {
            $this->defTimeOut = $this->connection->getCallVoteTimeOut();
            $this->defTimeOut = $this->defTimeOut['CurrentValue'];
            if ($this->debug) echo "[exp\Votes] Disabling default timeout (".$this->defTimeOut.")... ";
            try {
                $this->connection->setCallVoteTimeOut(1);  // can't set to 0..  disallows custom votes..  :\
            } catch (\Exception $e) {
                $this->exp_chatSendServerMessage(__("Error: %s", $login, $e->getMessage()));
                if ($this->debug) echo "failed.";
            }
            if ($this->debug) echo "\n";
        }*/

        if ($this->isPluginLoaded('eXpansion\Menu')) {
            if ($this->debug) echo "[exp\Votes] Building eXp\Menu buttons..\n";
            if ($this->config->restartVote_enable || $this->config->skipVote_enable) {
                $this->callPublicMethod('eXpansion\Menu', 'addSeparator', __('Votes'), false);
            }
            if ($this->config->restartVote_enable) {
                $this->callPublicMethod('eXpansion\Menu', 'addItem', __('Restart Map'), null, array($this, 'vote_Restart'), false);
            }
            if ($this->config->skipVote_enable) {
                $this->callPublicMethod('eXpansion\Menu', 'addItem', __('Skip Map'), null, array($this, 'vote_Skip'), false);
            }
            $this->callPublicMethod('eXpansion\Menu', 'addItem', __('Cancel Vote'), null, array($this, 'cancelVote'), true);
        }

        if ($this->isPluginLoaded('Standard\Menubar')) {
            if ($this->debug) echo "[exp\Votes] Building Standard Menubar buttons..";
            $this->callPublicMethod('Standard\Menubar', 'addButton', 'Restart Map Vote', array($this, 'vote_Restart'), false);
            $this->callPublicMethod('Standard\Menubar', 'addButton', 'Skip Map Vote', array($this, 'vote_Skip'), false);
            $this->callPublicMethod('Standard\Menubar', 'addButton', 'Cancel Vote', array($this, 'cancelVote'), true);
        }
    }

    public function onBeginMatch () {

        $this->timer = time();
        if ($this->debug) echo "[exp\Votes] Timer set..\n";
    }

    public function onEndMap ($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {

        //$this->connection->cancelVote();
    }

    /**
     * Called when the Oliverde8HudMenu is loaded
     * @param \ManiaLivePlugins\oliverde8\HudMenu\HudMenu
     */
    public function onOliverde8HudMenuReady($menu) {

        if ($this->debug) echo "[exp\Votes] Building Oliverde8Hud submenu..\n";

        if ($this->config->restartVote_enable || $this->config->skipVote_enable) {

            $parent = $menu->findButton(array("menu", "Votes"));
            if (!$parent) {
                $button["style"] = "BgRaceScore2";
                $button["substyle"] = "SandTimer";
                $parent = $menu->addButton("menu", "Votes", $button);
            }

            if ($this->config->restartVote_enable) {
                $button["style"] = "Icons64x64_1";
                $button["substyle"] = "Refresh";
                $button["plugin"] = $this;
                $button["function"] = "vote_Restart";
                $menu->addButton($parent, "Restart Map", $button);
            }

            if ($this->config->skipVote_enable) {
                $button["style"] = "Icons64x64_1";
                $button["substyle"] = "ClipPlay";
                $button["plugin"] = $this;
                $button["function"] = "vote_Skip";
                $menu->addButton($parent, "Skip Map", $button);
            }
        }

        $this->hudMenuAdminButtons($menu);
    }

    private function hudMenuAdminButtons($menu){

        $parent = $menu->findButton(array('admin', 'Basic Commands'));
        if (!$parent) {
            $parent = $menu->findButton('admin');  // no basic cmd submenu?  just dump it in Admin..
        }
        $button["style"] = "Icons64x64_1";
        $button["substyle"] = "ClipPause";
        $button["plugin"] = $this;
        $button["function"] = 'cancelVote';
        $button["permission"] = "cancel_vote";
        $menu->addButton($parent, "Cancel Vote", $button);
    }

    public function vote_Restart ($login) {

        $this->voter = $login;

        if ($this->useQueue) {
            if ($this->debug) echo "[exp\Votes] Calling Restart (queue) vote..\n";
            $vote = new \DedicatedApi\Structures\Vote();
            $vote->callerLogin = $this->voter;
            $vote->cmdName = "Replay";
            $vote->cmdParam = array("the current map");
            $this->connection->callVote($vote, $this->config->restartVote_ratio, ($this->config->restartVote_timeout * 1000), $this->config->restartVote_voters);
        } else {
            if ($this->debug) echo "[exp\Votes] Calling Restart vote..\n";
            $this->connection->callVoteRestartMap($this->config->restartVote_ratio, ($this->config->restartVote_timeout * 1000), $this->config->restartVote_voters);
        }

        $player = $this->storage->getPlayerObject($login);
        $msg = exp_getMessage('#variable#%1$s #rank#initiated restart map vote..');
        $this->exp_chatSendServerMessage($msg, null, array(\ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm')));
    }

    public function vote_Skip ($login) {

        if (!$this->config->skipVote_limit) {
        } else {
            if (time() > $this->timer + $this->config->skipVote_limit) {
                $msg = exp_getMessage('#admin_error# $iMust run skip vote within first %1$s seconds of match start..');
                $this->exp_chatSendServerMessage($msg, $login, array($this->config->skipVote_limit));
                return;
            }
        }
        $this->voter = $login;
        if ($this->debug) echo "[exp\Votes] Calling skip vote..\n";
        $this->connection->callVoteNextMap($this->config->skipVote_ratio, ($this->config->skipVote_timeout * 1000), $this->config->skipVote_voters);

        $player = $this->storage->getPlayerObject($login);
        $msg = exp_getMessage('#variable#%1$s #rank#initiated skip map vote..');
        $this->exp_chatSendServerMessage($msg, null, array(\ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm')));
    }

    public function onVoteUpdated($stateName, $login, $cmdName, $cmdParam) {

        if ($this->debug) echo "[exp\Votes] Vote Status: " . $stateName . " -> " . $login . " -> " . $cmdName . " -> " . $cmdParam . "\n";

        switch ($cmdName) {
            case "Replay":
                switch ($stateName) {
                    case "VotePassed":
                        $msg = exp_getMessage('#record# $iVote passed!');
                        $this->exp_chatSendServerMessage($msg, null);
                        $this->callPublicMethod('eXpansion\\Maps', 'replayMap', $this->voter);
                        $this->voter = null;
                        break;
                    case "VoteFailed":
                        $msg = exp_getMessage('#admin_error# $iVote failed!');
                        $this->exp_chatSendServerMessage($msg, null);
                        $this->voter = null;
                        break;
                    default:
                        break;
                }
                break;
            default:
                break;
        }
    }

    function cancelVote ($login) {
        $player = $this->storage->getPlayerObject($login);
        $this->connection->cancelVote();
        $msg = exp_getMessage('#admin_action#Admin #variable#%1$s #admin_action# cancelled the vote!');
        $this->exp_chatSendServerMessage($msg, null, array(\ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm'), $login));
    }

    public function onUnload() {
        parent::onUnload();

        /*if ($this->config->defaultVotes_disable) {
            if ($this->debug) echo "[exp\Votes] Resetting CallVote Timeout (".$this->defTimeOut.")... ";
            try {
                $this->connection->setCallVoteTimeOut($this->defTimeOut);
            } catch (\Exception $e) {
                if ($this->debug) echo "failed.";
                $this->exp_chatSendServerMessage(__("Error: %s", $login, $e->getMessage()));
            }
            if ($this->debug) echo "\n";
        }*/
    }

}

?>
