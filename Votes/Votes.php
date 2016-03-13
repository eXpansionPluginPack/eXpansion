<?php

namespace ManiaLivePlugins\eXpansion\Votes;

use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\Votes\Gui\Windows\VoteSettingsWindow;

class Votes extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{
    /** @var Config */
    private $config;
    private $useQueue = false;
    private $timer = 0;
    private $voter = "";
    private $counters = array();
    private $update = false;
    private $resCount = 0;
    private $lastMapUid = "";

    function exp_onInit()
    {
        $this->config = Config::getInstance();
    }

    /**
     * returns managedvote with key of command name
     * @return \ManiaLivePlugins\eXpansion\Votes\Structures\ManagedVote[]
     */
    private function getVotes()
    {
        $out = array();
        for ($x = 0; $x < count($this->config->managedVote_commands); $x++) {
            $vote                = new Structures\ManagedVote();
            $vote->managed       = $this->config->managedVote_enable[$this->config->managedVote_commands[$x]];
            $vote->command       = $this->config->managedVote_commands[$x];
            $vote->ratio         = $this->config->managedVote_ratios[$this->config->managedVote_commands[$x]];
            $vote->timeout       = $this->config->managedVote_timeouts[$this->config->managedVote_commands[$x]];
            $vote->voters        = $this->config->managedVote_voters[$this->config->managedVote_commands[$x]];
            $out[$vote->command] = $vote;
        }
        return $out;
    }

    public function exp_onLoad()
    {
        parent::exp_onLoad();

        $this->enableDedicatedEvents();
        $this->enableTickerEvent();

        $cmd       = $this->registerChatCommand("replay", "vote_Restart", 0, true);
        $cmd->help = 'Start a vote to restart a map';
        $cmd       = $this->registerChatCommand("restart", "vote_Restart", 0, true);
        $cmd->help = 'Start a vote to restart a map';
        $cmd       = $this->registerChatCommand("res", "vote_Restart", 0, true);
        $cmd->help = 'Start a vote to restart a map';

        $cmd       = $this->registerChatCommand("skip", "vote_Skip", 0, true);
        $cmd->help = 'Start a vote to skip a map';

//$cmd = $this->addAdminCommand("poll", $this, "vote_Poll", null);
//$cmd->setHelp("Run a yes/no vote with the entered question");

        $cmd          = AdminGroups::addAdminCommand('cancelvote', $this, 'cancelVote', 'cancel_vote');
        $cmd->setHelp = 'Cancel current running callvote';
        AdminGroups::addAlias($cmd, "cancel");
    }

    public function exp_onReady()
    {
        $this->counters = array();
        $this->timer    = time();
        $this->setPublicMethod("vote_restart");
        $this->setPublicMethod("vote_skip");
        $this->setPublicMethod("showVotesConfig");

        $cmd = AdminGroups::addAdminCommand('votes', $this, 'showVotesConfig', 'server_votes'); //
        $cmd->setHelp('shows config window for managing votes');
        $cmd->setMinParam(0);


        $this->lastMapUid = $this->storage->currentMap->uId;
        
        if ($this->isPluginLoaded('\ManiaLivePlugins\\eXpansion\\Maps\\Maps') && $this->config->restartVote_useQueue) {
            $this->useQueue = true;
            $this->debug("[exp\Votes] Restart votes set to queue");
        } else {
            $this->debug("[exp\Votes] Restart vote set to normal");
        }

        $this->syncSettings();
    }

    public function syncSettings()
    {

        $managedVotes = $this->getVotes();

        foreach ($managedVotes as $cmd => $vote) {
            $ratios[] = new \Maniaplanet\DedicatedServer\Structures\VoteRatio($vote->command, $vote->ratio);
        }
        $this->connection->setCallVoteRatios($ratios, false);
        if ($this->config->use_votes == false) $this->connection->setCallVoteTimeOut(0);
        else {
            $this->connection->setCallVoteTimeOut(($this->config->global_timeout * 1000));
        }
    }

    public function onBeginMatch()
    {
        $this->counters = array();
        $this->timer    = time();

        if ($this->storage->currentMap->uId == $this->lastMapUid) $this->resCount++;
        else {
            $this->lastMapUid = $this->storage->currentMap->uId;
            $this->resCount   = 0;
        }
    }

    public function onTick()
    {
        if ($this->update) {
            $this->update = false;
            $this->syncSettings();
        }
    }

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap)
    {
        //$this->connection->cancelVote();
    }

    public function vote_Restart($login)
    {
        try {
            $managedVotes = $this->getVotes();

            // if vote is not managed...
            if (!array_key_exists('RestartMap', $managedVotes)) return;
            // if vote is not managed...
            if ($managedVotes['RestartMap']->managed == false) return;

            $config = Config::getInstance();  
            if ($config->restartLimit != 0 && $config->restartLimit < $this->resCount) {                              
                $this->exp_chatSendServerMessage(exp_getMessage("#error#Map limit for voting restart reached."), $login, array($this->config->restartLimit));
                return;
            }

            $this->voter       = $login;
            $vote              = $managedVotes['RestartMap'];
            $this->debug("[exp\\Votes] Calling Restart (queue) vote..");
            $vote->callerLogin = $this->voter;
            $vote->cmdName     = "Replay";
            $vote->cmdParam    = array("the current map");
            $this->connection->callVote($vote, $vote->ratio, ($vote->timeout * 1000), $vote->voters);

            $player = $this->storage->getPlayerObject($login);
            $msg    = exp_getMessage('#variable#%s #vote#initiated restart map vote..');
            $this->exp_chatSendServerMessage($msg, null, array(\ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm')));
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage("[Notice] ".$e->getMessage(), $login);
        }
    }

    public function vote_Skip($login)
    {

        try {
            $managedVotes = $this->getVotes();
            // if vote is not managed...
            if (!array_key_exists('NextMap', $managedVotes)) return;
            // if vote is not managed...
            if ($managedVotes['NextMap']->managed == false) return;

            $this->voter       = $login;
            $vote              = $managedVotes['NextMap'];
            $this->debug("[exp\Votes] Calling Skip vote..");
            $vote->callerLogin = $this->voter;
            $vote->cmdName     = "Skip";
            $vote->cmdParam    = array("the current map");
            $this->connection->callVote($vote, $vote->ratio, ($vote->timeout * 1000), $vote->voters);

            $player = $this->storage->getPlayerObject($login);
            $msg    = exp_getMessage('#variable#%1$s #vote#initiated skip map vote..');
            $this->exp_chatSendServerMessage($msg, null, array(\ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm')));
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage("[Notice] ".$e->getMessage(), $login);
        }
    }

    public function onVoteUpdated($stateName, $login, $cmdName, $cmdParam)
    {

        //$this->console("[exp\Votes] Vote Status: " . $stateName . " -> " . $login . " -> " . $cmdName . " -> " . $cmdParam);
// in case managed votes are disabled, return..
        if ($this->config->use_votes == false) return;

        $managedVotes = $this->getVotes();

// disable default votes... and replace them with our own implementations
        if ($stateName == "NewVote") {
            if ($cmdName == "RestartMap") {
                $this->connection->cancelVote();
                $this->voter = $login;
                $this->vote_Restart($login);
                return;
            }
            if ($cmdName == "SkipMap") {
                $this->connection->cancelVote();
                $this->voter = $login;
                $this->vote_Skip($login);
                return;
            }
        }
// check for our stuff...	
        if ($stateName == "NewVote") {
            $login = $this->voter;
            foreach ($managedVotes as $cmd => $vote) {
                if ($cmdName == $cmd) {
                    if ($vote->ratio == -1.) {
                        $this->cancelVote($login);
                    }
                }
            }

            if (!isset($this->counters[$login][$cmdName])) {
                $this->counters[$login][$cmdName] = 0;
            }

            $this->counters[$login][$cmdName] ++;

            if ($this->config->limit_votes != -1) {
                if ($this->counters[$login][$cmdName] > $this->config->limit_votes) {

                    $this->connection->cancelVote();
                    $msg = exp_getMessage("Vote limit reached.");
                    $this->exp_chatSendServerMessage($msg);
                    return;
                }
            }
        }


// own votes handling...

        if ($stateName == "VotePassed") {
            if ($cmdName != "Replay" && $cmdName != "Skip") return;

            $msg   = exp_getMessage('#vote_success# $iVote passed!');
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
            if ($cmdName != "Replay" && $cmdName != "Skip") return;
            $msg         = exp_getMessage('#vote_failure# $iVote failed!');
            $this->exp_chatSendServerMessage($msg, null);
            $this->voter = null;
        }
    }

    function cancelVote($login)
    {
        $player = $this->storage->getPlayerObject($login);
        $vote   = $this->connection->getCurrentCallVote();
        if (!empty($vote->cmdName)) {
            $this->connection->cancelVote();
            $msg = exp_getMessage('#admin_action#Admin #variable#%1$s #admin_action# cancelled the vote!');
            $this->exp_chatSendServerMessage($msg, null, array(\ManiaLib\Utils\Formatting::stripCodes($player->nickName, 'wosnm'), $login));
            return;
        } else {
            $this->connection->chatSendServerMessage('Notice: Can\'t cancel a vote, no vote in progress!', $login);
        }
    }

    public function showVotesConfig($login)
    {
        $window = Gui\Windows\VoteSettingsWindow::Create($login);
        $window->setSize(120, 96);
        $window->setTitle(__("Configure Votes", $login));
        $window->populateList($this->getVotes(), $this->metaData);
        $window->show($login);
    }

    public function exp_onUnload()
    {

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
        VoteSettingsWindow::EraseAll();
    }

    public function onSettingsChanged(\ManiaLivePlugins\eXpansion\Core\types\config\Variable $var)
    {
        if ($var->getConfigInstance() instanceof Config) {
            $this->update = true;
        }
    }
}
?>
