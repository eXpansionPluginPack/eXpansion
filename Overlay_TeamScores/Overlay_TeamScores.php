<?php

namespace ManiaLivePlugins\eXpansion\Overlay_TeamScores;

class Overlay_TeamScores extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    /** @var Structures\Team[] */
    private $teams = array();

    /** @var DedicatedApi\Structures\Status */
    private $status;

    public function exp_onInit() {
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TEAM);
    }

    public function exp_onLoad() {
        $this->enableDedicatedEvents();
        $this->teams[0] = new Structures\Team('$00FTeam Blue');
        $this->teams[1] = new Structures\Team('$F00Team Red');
    }

    public function exp_onReady() {
        $this->showWidget('reaby');
    }

    public function showWidget($login = null) {
        if ($this->storage->serverStatus->code != \DedicatedApi\Structures\Status::PLAY) {
            return;
        }               
        if ($login == null) {
            $logins = array();
            foreach ($this->storage->players as $player)
                $logins[] = $player->login;

            $group = \ManiaLive\Gui\Group::Create("spectators", $logins);
            $widget = Gui\Widgets\ScoresOverlay::Create($group);
            $widget->setData($this->teams);
            $widget->show();
            return;
        }

        $widget = Gui\Widgets\ScoresOverlay::Create($login);
        $widget->setData($this->teams);
        $widget->show($login);
    }

    public function hideWidget($login = null) {
        if ($login == null) {
            Gui\Widgets\ScoresOverlay::EraseAll();
            return;
        }
        $widget = Gui\Widgets\ScoresOverlay::Erase($login);
    }

    public function onPlayerInfoChanged($playerInfo) {
        if ($playerInfo['SpectatorStatus'] == 2551101)
            $this->showWidget($playerInfo['Login']);
        if ($playerInfo['SpectatorStatus'] == 0)
            $this->hideWidget($playerInfo['Login']);
    }

    public function onEndRound() {
        $scores = $this->connection->getCurrentRanking(2, 0);
        $this->teams[0]->score = $scores[0]->score;
        $this->teams[1]->score = $scores[1]->score;

        $this->showWidget();
    }

}