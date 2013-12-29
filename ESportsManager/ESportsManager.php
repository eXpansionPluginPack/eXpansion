<?php

namespace ManiaLivePlugins\eXpansion\ESportsManager;

use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\ESportsManager\Structures\MatchStatus;
use ManiaLivePlugins\eXpansion\ESportsManager\Structures\PlayerStatus;
use ManiaLivePlugins\eXpansion\ESportsManager\Structures\MatchSetting;

/**
 * Description of ESportsMAnager
 *
 * @author Reaby
 */
class ESportsManager extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $actions_matchReady = array();
    private $action_matchSelect;

    /** @var Structures\PlayerStatus[] */
    public static $playerStatuses = array();

    /** @var \ManiaLive\Gui\ActionHandler */
    private $aHandler;

    /** @var MatchStatus */
    public static $matchStatus;

    /** @var MatchSetting */
    public static $matchSettings;

    /** @var MatchSetting */
    public static $nextMatchSettings;

    /** @var Structures\MatchCounter */
    private $matchCounter;

    /** @var Config */
    private $config;

    public function exp_onLoad() {
        self::$matchStatus = new Structures\MatchStatus();
        self::$matchSettings = new MatchSetting();
        self::$nextMatchSettings = null;

        $this->matchCounter = new Structures\MatchCounter();
        $this->config = Config::getInstance();
    }

    public function exp_onReady() {
        $this->enableTickerEvent();
        $this->enableDedicatedEvents();
        $this->connection->manualFlowControlEnable(true);

        $this->aHandler = \ManiaLive\Gui\ActionHandler::getInstance();
        $admingroup = AdminGroups::getInstance();

        $cmd = AdminGroups::addAdminCommand('esports stop', $this, 'matchStop', 'esports_admin');
        $cmd->setHelp('Sends a "Stop Match" window to players');
        $cmd->setMinParam(0);
        AdminGroups::addAlias($cmd, "stop");

        $cmd = AdminGroups::addAdminCommand('esports askready', $this, 'matchAskReady', 'esports_admin');
        $cmd->setHelp('Asks if players are ready');
        $cmd->setMinParam(0);
        AdminGroups::addAlias($cmd, "ready");


        $cmd = AdminGroups::addAdminCommand('esports select', $this, 'matchSelect', 'esports_admin');
        $cmd->setHelp('Select match');
        $cmd->setMinParam(0);
        AdminGroups::addAlias($cmd, "select");

// actions for matchReady widget
        $this->actions_matchReady["ready"] = $this->aHandler->createAction(array($this, "setReady"));
        $this->actions_matchReady["notReady"] = $this->aHandler->createAction(array($this, "setNotReady"));
        $this->actions_matchReady["spec"] = $this->aHandler->createAction(array($this, "togglespec"), "spec");
        $this->actions_matchReady["play"] = $this->aHandler->createAction(array($this, "togglespec"), "play");
        $this->actions_matchReady["joinTeam0"] = $this->aHandler->createAction(array($this, "changeTeam"), 0);
        $this->actions_matchReady["joinTeam1"] = $this->aHandler->createAction(array($this, "changeTeam"), 1);
        $this->actions_matchReady["forceContinue"] = $this->aHandler->createAction(array($this, "chatDoContinue"));
        $this->action_matchSelect = $this->aHandler->createAction(array($this, "matchSelect"));
        Gui\Widgets\MatchReady::$actions = $this->actions_matchReady;

        $this->generatePlayerStatuses();

// @todo remove this debug variable
        self::$matchStatus->isMatchRunning = true;
        self::$matchStatus->isMatchActive = true;

        $cmd = AdminGroups::addAdminCommand('esports go', $this, 'chatDoContinue', 'esports_admin');
        $cmd->setHelp('Force Continue');
        $cmd->setMinParam(0);
        AdminGroups::addAlias($cmd, "go");
    }

    public function togglespec($login, $status) {
        switch ($status) {
            case "play":
                $this->connection->forceSpectator($login, 2);
                $this->connection->forceSpectator($login, 0);
                break;
            case "spec":
                $this->connection->forceSpectator($login, 3);
                break;
        }
    }

    public function changeTeam($login, $team) {
        $this->connection->forcePlayerTeam($login, $team);
    }

    /**
     * generates totally new playerStatuses objects 
     * @return \ManiaLivePlugins\eXpansion\ESportsManager\Structures\PlayerStatus[]
     */
    private function generatePlayerStatuses() {
        self::$playerStatuses = array();
        foreach ($this->storage->players as $login => $player) {
            self::$playerStatuses[$login] = new PlayerStatus($player);
        }
    }

    public function onBeginMap($map, $warmUp, $matchContinuation) {
        self::$nextMatchSettings = null;
        if ($warmUp) {
            self::$matchStatus->warmUp = true;
        }
    }

    public function onManualFlowControlTransition($transition) {
        if (self::$matchStatus->isMatchActive == false) {
            $this->connection->manualFlowControlProceed();
            return;
        }
        echo $transition . "\n";
        switch ($transition) {
            case "Play -> Podium":
                if (self::$matchStatus->warmUp == true) {
                    self::$matchStatus->warmUp = false;
                    $this->doContinue(null, true);
                } elseif (self::$nextMatchSettings === null) {
                    self::$matchStatus->voteRunning = MatchStatus::VOTE_SELECTMATCH;
                    $this->sendSelectMatchWindow();
                } else {
                    self::$matchStatus->warmUp = false;
                    $this->setNextMatchParameters();
                    $this->doContinue(null, false);
                }
                break;
            case "Podium -> Synchro":
                self::$matchStatus->isAllPlayersReady = false;
                $this->doContinue(null, false);
                break;
            // on map start
            case "Synchro -> Play":
                if (self::$matchStatus->warmUp == true) {
                    $this->doContinue(null, true);
                } elseif (self::$matchStatus->isAllPlayersReady == false) {
                    $this->generatePlayerStatuses();
                    $this->matchAskReady(null);
                } else {
                    $this->doContinue(null, true);
                }
                break;
            // on restart or match has stopped or new round
            case "Play -> Synchro":
                if (self::$matchStatus->isAllPlayersReady == false) {
                    $this->generatePlayerStatuses();
                    $this->matchAskReady(null);
                } else {
                    $this->doContinue(null, true);
                }
                break;
        }
    }

    public function setNextMatchParameters() {
        self::$matchSettings = self::$nextMatchSettings;

        $this->connection->setGameInfos(self::$nextMatchSettings->gameInfos);
        $agroups = AdminGroups::getInstance();
        $login = $this->findAdmin();
        if ($login) {
            foreach (self::$nextMatchSettings->adminCommands as $line) {
                if (empty($line))
                    continue;
                if (strpos($line, "/adm")) {
                    $ling = str_replace("/adm", "", $line);
                    $agroups->adminCmd($login, $line);
                }
            }
        } else {
            $this->exp_chatSendServerMessage("Didn't find admin on server, chat triggers not executed.");
        }
    }

    private function findAdmin() {
        foreach ($this->storage->players as $login => $player) {
            if (AdminGroups::hasPermission($login, "esports_admin"))
                return $login;
        }
        foreach ($this->storage->spectators as $login => $player) {
            if (AdminGroups::hasPermission($login, "esports_admin"))
                return $login;
        }
        return false;
    }

    public function sendSelectMatchWindow($fromLogin = null) {
        foreach ($this->storage->players as $login => $player) {
            $window = Gui\Windows\MatchWait::Create($login);
            if (AdminGroups::hasPermission($login, "esports_admin")) {
                $window->setAdminAction($this->action_matchSelect, $this->actions_matchReady["forceContinue"]);
            }
            $window->setSize(90, 30);
            $window->show();
        }
        foreach ($this->storage->spectators as $login => $player) {
            $window = Gui\Windows\MatchWait::Create($login);
            if (AdminGroups::hasPermission($login, "esports_admin")) {
                $window->setAdminAction($this->action_matchSelect, $this->actions_matchReady["forceContinue"]);
            }
            $window->setSize(90, 30);
            $window->show();
        }
    }

    public function onMapRestart() {
        self::$matchStatus->isAllPlayersReady = false;
    }

    public function onMapSkip() {
        self::$matchStatus->isAllPlayersReady = false;
    }

    public function chatDoContinue($login) {
        $this->doContinue($login, true);
    }

    public function doContinue($login = null, $clear = false) {
        if ($clear) {
            if (!self::$matchStatus->isAllPlayersReady) {
                self::$matchStatus->isAllPlayersReady = true;
            }
            if (!self::$matchStatus->isMatchRunning) {
                self::$matchStatus->isMatchRunning = true;
            }
        }
        Gui\Windows\HaltMatch::EraseAll();
        Gui\Widgets\MatchReady::EraseAll();
        Gui\Windows\MatchWait::EraseAll();
        Gui\Windows\MatchSelect::EraseAll();
        if ($this->connection->manualFlowControlGetCurTransition() != '')
            $this->connection->manualFlowControlProceed();
    }

    /**
     *  
     */
    private function checkPlayerStatuses() {
        foreach ($this->storage->players as $player) {
            if (!array_key_exists($player->login, self::$playerStatuses)) {
                if (!is_object(self::$playerStatuses[$player->login])) {
                    echo "creating new playerstatus\n";
                    self::$playerStatuses[$player->login] = new PlayerStatus($player);
                }
            }
        }
    }

    public function showReadyWidgetToAll() {
        foreach ($this->storage->players as $login => $player)
            $this->showReadyWidget($login);

        foreach ($this->storage->spectators as $login => $player)
            $this->showReadyWidget($login);
    }

    public function matchSelect($login) {
        Gui\Windows\MatchSelect::Erase($login);
        $widget = Gui\Windows\MatchSelect::Create($login);
        $widget->setSize(60, 90);
        $widget->centerOnScreen();
        $widget->show();
    }

    public function showReadyWidget($login) {
        Gui\Widgets\MatchReady::Erase($login);
        $widget = Gui\Widgets\MatchReady::Create($login);
        $widget->setGamemode($this->storage->gameInfos->gameMode);
        $widget->setPosition(0, 0);
        $widget->setSize(160, 160);
        $widget->show();
    }

    public function matchAskReady($fromLogin) {
        $this->generatePlayerStatuses();
        self::$matchStatus->isAllPlayersReady = false;

        if (!self::$matchStatus->isMatchRunning)
            return;

        self::$matchStatus->voteRunning = MatchStatus::VOTE_READY;
        $this->showReadyWidgetToAll();
    }

    public function onPlayerInfoChanged($playerInfo) {
        $player = \DedicatedApi\Structures\Player::fromArray($playerInfo);
        $login = $player->login;

        if ($player->playerId == 0)
            return;

        if ($player->spectator == true) {
            unset(self::$playerStatuses[$login]);
        } else {
            self::$playerStatuses[$login] = new PlayerStatus($player);
            self::$playerStatuses[$login]->player->teamId = $player->teamId;
        }


// handle votes
        if (self::$matchStatus->voteRunning == MatchStatus::VOTE_READY) {
            Gui\Widgets\MatchReady::RedrawAll();
        }
    }

    public function onPlayerConnect($login, $isSpectator) {
        $this->checkPlayerStatuses();
        if (self::$matchStatus->voteRunning) {
            switch (self::$matchStatus->voteRunning) {
                case MatchStatus::VOTE_READY:
                    $this->showReadyWidget($login);
                    break;
                case MatchStatus::VOTE_SELECTMATCH:
                    $this->sendSelectMatchWindow($login);
                    break;
            }
        }
    }

    public function onPlayerDisconnect($login, $disconnectionReason) {
        if (array_key_exists($login, self::$playerStatuses)) {
            unset(self::$playerStatuses[$login]);
        }
    }

    public function setReady($login) {
        $player = $this->storage->getPlayerObject($login);
        if ($player == null)
            return;
        $this->checkPlayerStatuses();
        self::$playerStatuses[$login]->status = PlayerStatus::Ready;
        self::$playerStatuses[$login]->voteStartTime = time();
        $this->showReadyWidgetToAll();
    }

    public function setNotReady($login) {
        $player = $this->storage->getPlayerObject($login);
        if ($player == null)
            return;
        $this->checkPlayerStatuses();
        self::$playerStatuses[$login]->status = PlayerStatus::NotReady;
        self::$playerStatuses[$login]->voteStartTime = time();
        $this->showReadyWidgetToAll();
    }

    public function onTick() {
        switch (self::$matchStatus->voteRunning) {
            case MatchStatus::VOTE_READY:
                $this->onTickVoteReady();
                break;
            case MatchStatus::VOTE_SELECTMATCH:
                $this->onTickVoteMatch();
                break;
        }
    }

    public function onTickVoteMatch() {
        if (self::$nextMatchSettings !== null) {
            self::$matchStatus->voteRunning = MatchStatus::VOTE_NONE;
            self::$matchStatus->isAllPlayersReady = false;
            $this->setNextMatchParameters();
            Gui\Windows\MatchWait::EraseAll();
            Gui\Windows\MatchSelect::EraseAll();
            $this->doContinue(null, true);
        }
    }

    public function onTickVoteReady() {
        $forceupdate = false;
        $playersCount = count($this->storage->players);
        $readyCount = 0;
        foreach (self::$playerStatuses as $login => $player) {
            if (($player->voteStartTime + $this->config->readyTimeout) < time()) {
                self::$playerStatuses[$login]->status = PlayerStatus::Timeout;
                self::$playerStatuses[$login]->voteStartTime = time();
                $forceupdate = true;
            }
            if ($player->status == PlayerStatus::Ready)
                $readyCount ++;
        }
        if ($forceupdate) {
            $this->showReadyWidgetToAll();
        }

        if ($readyCount == $playersCount) {
            self::$matchStatus->voteRunning = MatchStatus::VOTE_NONE;
            self::$matchStatus->isAllPlayersReady = true;
            Gui\Widgets\MatchReady::EraseAll();
            Gui\Windows\HaltMatch::EraseAll();
            $this->doContinue();
        }
    }

    public function chatMatch($login, $params) {
        $action = array_shift($params);
        switch ($action) {
            case "stop":
                $this->matchStop($login, $params);
                break;
            case "ready":
                $this->matchAskReady($login);
                break;
        }
    }

    public function matchStop($login, $params) {
        self::$matchStatus->isAllPlayersReady = false;
        $this->connection->forceEndRound();

        $reason = "Admin has requested to stop the match!";
        if (isset($params[0])) {
            $reason = implode(" ", $params);
        }

        foreach ($this->storage->players as $login => $player) {
            $window = Gui\Windows\HaltMatch::Create($login);
            $window->setReason($reason);
            $window->setSize(90, 30);
            $window->show();
        }
    }

}
