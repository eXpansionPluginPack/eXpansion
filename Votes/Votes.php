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
    private $voter = null;

    function exp_onInit() {

        $this->config = Config::getInstance();
        $ratios = $this->connection->getCallVoteRatios();
        foreach ($ratios as $ratio) {
            if ($ratio->command == "*") {
                $this->config->restartVote_ratio = floatval($ratio->ratio);
                $this->config->skipVote_ratio = floatval($ratio->ratio);
            }
            if ($ratio->command == "RestartMap")
                $this->config->restartVote_ratio = floatval($ratio->ratio);
            if ($ratio->command == "NextMap")
                $this->config->skipVote_ratio = floatval($ratio->ratio);
        }

        if ($this->config->restartVote_ratio == -1)
            $this->config->restartVote_ratio = 1;
//Oliverde8 Menu
        if ($this->isPluginLoaded('\ManiaLivePlugins\oliverde8\HudMenu\HudMenu'))
            Dispatcher::register(\ManiaLivePlugins\oliverde8\HudMenu\onOliverde8HudMenuReady::getClass(), $this);
    }

    public function exp_onLoad() {
        parent::exp_onLoad();

        $this->enableDedicatedEvents();

        if ($this->config->restartVote_enable) {
            $cmd = $this->registerChatCommand("replay", "vote_Restart", 0, true);
            $cmd->help = 'Start a vote to restart a map';
            $cmd = $this->registerChatCommand("restart", "vote_Restart", 0, true);
            $cmd->help = 'Start a vote to restart a map';
            $cmd = $this->registerChatCommand("res", "vote_Restart", 0, true);
            $cmd->help = 'Start a vote to restart a map';
        }
        if ($this->config->skipVote_enable) {
            $cmd = $this->registerChatCommand("skip", "vote_Skip", 0, true);
            $cmd->help = 'Start a vote to skip a map';
        }
//$cmd = $this->addAdminCommand("poll", $this, "vote_Poll", null);
//$cmd->setHelp("Run a yes/no vote with the entered question");

        $cmd = AdminGroups::addAdminCommand('cancelvote', $this, 'cancelVote', 'cancel_vote');
        $cmd->setHelp = 'Cancel current running callvote';
        AdminGroups::addAlias($cmd, "cancel");
    }

    public function exp_onReady() {
      
        $this->timer = time();
        $this->setPublicMethod("vote_restart");
        $this->setPublicMethod("vote_skip");

        if ($this->isPluginLoaded('\ManiaLivePlugins\\eXpansion\\Maps\\Maps') && $this->config->restartVote_useQueue) {
            $this->useQueue = true;
            $this->debug("[exp\Votes] Restart votes set to queue");
        } else {
            $this->debug("[exp\Votes] Restart vote set to normal");
        }

        /* if ($this->config->defaultVotes_disable) {
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
          } */

        if ($this->isPluginLoaded('eXpansion\Menu')) {
            $this->debug("[exp\Votes] Building eXp\Menu buttons..");
            if ($this->config->restartVote_enable || $this->config->skipVote_enable) {
                $this->callPublicMethod('\ManiaLivePlugins\eXpansion\Menu', 'addSeparator', __('Votes'), false);
            }
            if ($this->config->restartVote_enable) {
                $this->callPublicMethod('\ManiaLivePlugins\eXpansion\Menu', 'addItem', __('Restart Map'), null, array($this, 'vote_Restart'), false);
            }
            if ($this->config->skipVote_enable) {
                $this->callPublicMethod('\ManiaLivePlugins\eXpansion\Menu', 'addItem', __('Skip Map'), null, array($this, 'vote_Skip'), false);
            }
            $this->callPublicMethod('\ManiaLivePlugins\eXpansion\Menu', 'addItem', __('Cancel Vote'), null, array($this, 'cancelVote'), true);
        }
    }

    public function onBeginMatch() {

        $this->timer = time();
        $this->debug("[exp\Votes] Timer set..");
    }

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {
        //$this->connection->cancelVote();
    }

    /**
     * Called when the Oliverde8HudMenu is loaded
     * @param \ManiaLivePlugins\oliverde8\HudMenu\HudMenu
     */
    public function onOliverde8HudMenuReady($menu) {

        $this->debug("[exp\Votes] Building Oliverde8Hud submenu..");

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

    private function hudMenuAdminButtons($menu) {

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

    public function vote_Restart($login) {
        try {
            $this->voter = $login;


            $this->debug("[exp\Votes] Calling Restart (queue) vote..");
            $vote = new \Maniaplanet\DedicatedServer\Structures\Vote();
            $vote->callerLogin = $this->voter;
            $vote->cmdName = "Replay";
            $vote->cmdParam = array("the current map");
            $this->connection->callVote($vote, $this->config->restartVote_ratio, ($this->config->restartVote_timeout * 1000), $this->config->restartVote_voters);

            $player = $this->storage->getPlayerObject($login);
           $msg = exp_getMessage('#variable#%s #vote#initiated restart map vote..');	    
            $this->exp_chatSendServerMessage($msg, null, array(\ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm')));
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage("[Notice] " . $e->getMessage(), $login);
        }
    }

    public function vote_Skip($login) {
        try {
            $this->voter = $login;
            $this->debug("[exp\Votes] Calling skip vote..");
            $vote = new \Maniaplanet\DedicatedServer\Structures\Vote();
            $vote->callerLogin = $this->voter;
            $vote->cmdName = "Skip";
            $vote->cmdParam = array("the current map");
            $this->connection->callVote($vote, $this->config->skipVote_ratio, ($this->config->skipVote_timeout * 1000), $this->config->skipVote_voters);

            $player = $this->storage->getPlayerObject($login);
            $msg = exp_getMessage('#variable#%1$s #vote#initiated skip map vote..');
            $this->exp_chatSendServerMessage($msg, null, array(\ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm')));
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage("[Notice] " . $e->getMessage(), $login);
        }
    }

    public function onVoteUpdated($stateName, $login, $cmdName, $cmdParam) {

        $this->debug("[exp\Votes] Vote Status: " . $stateName . " -> " . $login . " -> " . $cmdName . " -> " . $cmdParam);

// disable default votes... and replace them with our own implementations
        if ($stateName == "NewVote") {
            if ($cmdName == "RestartMap") {
                $this->connection->cancelVote();
                $this->vote_Restart($login);
                return;
            }
            if ($cmdName == "SkipMap") {
                $this->connection->cancelVote();
                $this->vote_Skip($login);
                return;
            }
        }


// own votes handling...

        if ($stateName == "VotePassed") {
            if ($cmdName != "Replay" && $cmdName != "Skip")
                return;

            $msg = exp_getMessage('#vote_success# $iVote passed!');
            $this->exp_chatSendServerMessage($msg, null);
            $voter = $this->voter;
            if ($cmdName == "Replay") {
                if (sizeof($this->storage->players) == 1) {
                    $this->callPublicMethod('\ManiaLivePlugins\\eXpansion\\Maps\\Maps', 'replayMapInstant', $voter);
                } else {
                    $this->callPublicMethod('\ManiaLivePlugins\\eXpansion\\Maps\\Maps', 'replayMap', $voter);
                }
            }
            if ($cmdName == "Skip") {
                $this->connection->nextMap();
            }
            $this->voter = null;
        }
        if ($stateName == "VoteFailed") {
            if ($cmdName != "Replay" && $cmdName != "Skip")
                return;
            $msg = exp_getMessage('#vote_failure# $iVote failed!');
            $this->exp_chatSendServerMessage($msg, null);
            $this->voter = null;
        }
    }

    function cancelVote($login) {
        $player = $this->storage->getPlayerObject($login);
        $vote = $this->connection->getCurrentCallVote();
        if (!empty($vote->cmdName)) {
            $this->connection->cancelVote();
            $msg = exp_getMessage('#admin_action#Admin #variable#%1$s #admin_action# cancelled the vote!');
            $this->exp_chatSendServerMessage($msg, null, array(\ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm'), $login));
            return;
        } else {
            $this->connection->chatSendServerMessage('Notice: Can\'t cancel a vote, no vote in progress!', $login);
        }
    }

    public function exp_onUnload() {

        /* if ($this->config->defaultVotes_disable) {
          if ($this->debug) echo "[exp\Votes] Resetting CallVote Timeout (".$this->defTimeOut.")... ";
          try {
          $this->connection->setCallVoteTimeOut($this->defTimeOut);
          } catch (\Exception $e) {
          if ($this->debug) echo "failed.";
          $this->exp_chatSendServerMessage(__("Error: %s", $login, $e->getMessage()));
          }
          if ($this->debug) echo "\n";
          } */
    }

}

?>
