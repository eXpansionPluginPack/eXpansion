<?php

namespace ManiaLivePlugins\eXpansion\ESLcup;

/**
 * Description of ESLcup
 *
 * @author Reaby
 */
class ESLcup extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    /** @var ManiaLivePlugins\eXpansion\ESLcup\Structures\CupScore[] */
    private $cupScores = array();
    private $lastRoundWinner = "";
    private $pointsLimit = 100;
    private $resetData = false;

    /** @var \DedicatedApi\Structures\Player */
    private $roundFinish = array();

    /** @var ManiaLivePlugins\eXpansion\ESLcup\Structures\CupScore[] */
    private $winners = array();

    public function exp_onLoad() {
        $this->enableDedicatedEvents();
        $this->setPublicMethod("syncScores");
    }

    public function exp_onReady() {
        $this->syncScores();
        $this->hideUI();
        $this->scoreTable(true, "scorestable");

        $admingroup = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance();
        $cmd = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::addAdminCommand('game eslcup', $this, 'eslcup', 'game_settings');
        $admingroup->addShortAlias($cmd, 'eslcup');
    }

    function eslcup($fromLogin, $params) {

        try {
            $command = array_shift($params);

            switch (strtolower($command)) {
                case "pointslimit":
                case "limit":
                    $params = $params[0];
                    if (is_numeric($params)) {
                        $this->pointsLimit = $params;
                        $this->exp_chatSendServerMessage("ESLcup points limit is now set to:" . $params);
                        $newlimit = (intval($params) * 2 ) + 60;
                        $this->connection->setCupPointsLimit($newlimit);
                        $this->syncScores();
                        $this->Scoretable(true);
                    }
                    break;
                case "reset":
                    $this->winners = array();
                    $this->roundFinish = array();
                    $this->lastRoundWinner = "";
                    $this->resetData = true;
                    $this->connection->nextMap(false);
                    break;
                default:
                    $this->exp_chatSendServerMessage("command not found", $fromLogin);
                    break;
            }
        } catch (\Exception $e) {
            
        }
    }

    /**
     * SyncScores from player rankings
     */
    public function syncScores() {
        $this->cupScores = null;
        $ranking = $this->connection->getCurrentRanking(-1, 0);
        $this->cupScores = array();
        foreach ($ranking as $player) {
            $this->cupScores[$player->login] = new Structures\CupScore($player->playerId, $player->login, $player->nickName, $player->score);
            $pla = $this->storage->getPlayerObject($player->login);
            if (is_object($pla))
                $this->cupScores[$player->login]->isConnected = $pla->isConnected;
        }

        // adding pilxi to winners;
        /* $this->cupScores["pilxi"]->hasWin = time();
          $this->winners[0] = $this->cupScores["pilxi"];

          $this->cupScores["them"]->hasWin = time();
          $this->winners[1] = $this->cupScores["them"];
         */

        // find finalist!
        $finalists = array();
        foreach ($this->cupScores as $login => $score) {
            $this->cupScores[$score->login]->finalist = false;
            if ($score->score >= $this->pointsLimit) {
                if (!$this->cupScores[$score->login]->hasWin) {
                    $finalists[$login] = $score;
                }
            }
        }
        if (sizeof($finalists) > 0) {
            \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::asortDesc($finalists, "score");
            reset($finalists);
            $score = array_shift($finalists);
            $this->cupScores[$score->login]->finalist = true;
        }


        \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::asortDesc($this->cupScores, "score");

        $this->Scoretable(true, "scorestable");
    }

    public function onBeginRound() {
        $this->roundFinish = array();

        if ($this->resetData) {
            $this->winners = array();
            $this->roundFinish = array();
            $this->lastRoundWinner = "";
            $this->resetData = false;
        }

        $this->scoreTable(true, "scorestable");
    }

    public function onPlayerConnect($login, $isSpec) {
        $player = $this->storage->getPlayerObject($login);
        if (!array_key_exists($player->login, $this->cupScores)) {
            $this->cupScores[$player->login] = new Structures\CupScore($player->playerId, $player->login, $player->nickName, $player->score);
            $this->cupScores[$player->login]->isConnected = true;
        } else {
            $this->cupScores[$player->login]->isConnected = true;
        }
        $this->Scoretable(true, "scorestable");
    }

    public function onPlayerDisconnect($login, $disconnectionReason = null) {
        $player = $this->storage->getPlayerObject($login);
        if (!array_key_exists($player->login, $this->cupScores)) {
            $this->cupScores[$player->login]->isConnected = false;
        }
        $this->Scoretable(true, "scorestable");
    }

    public function onPlayerFinish($playerUid, $login, $timeOrScore) {
        if ($timeOrScore == 0)
            return;
        $player = new \DedicatedApi\Structures\Player();
        $player->playerId = $playerUid;
        $player->login = $login;
        $player->nickName = $this->storage->getPlayerObject($login)->nickName;
        $player->rank = count($this->roundFinish) + 1;
        $this->roundFinish[] = $player;
    }

    public function onEndRound() {
        $scores = $this->connection->getRoundCustomPoints();

        if (sizeof($scores) < 1) {
            $scores = array(10, 6, 4, 3, 2, 1);
        }

        foreach ($this->roundFinish as $player) {
            if (!array_key_exists($player->login, $this->cupScores)) {
                $scores[$player->login] = new Structures\CupScore($player->playerId, $player->login, $player->nickName, $player->score);
            } else {
                $points = 1;
                if (isset($scores[($player->rank - 1)])) {
                    $points = $scores[($player->rank - 1)];
                }
                $this->cupScores[$player->login]->score += $points;


                if ($this->cupScores[$player->login]->finalist) {
                    if ($this->lastRoundWinner == $player->login) {
                        $this->cupScores[$player->login]->hasWin = time();
                        $this->winners[] = $this->cupScores[$player->login];
                        $this->exp_chatSendServerMessage($player->nickName . ' $z$s takes the ' . count($this->winners) . ' place!');
                        if (count($this->winners) >= 3) {
                            $this->scoreTable(true, "normal");
                            $this->resetData = true;
                            $this->connection->nextMap(false);
                            return;
                        }
                    }
                }
            }
            if ($player->rank == 1) {
                $this->lastRoundWinner = $player->login;
            }
        }

        // find finalist!
        $finalists = array();
        foreach ($this->cupScores as $login => $score) {
            $this->cupScores[$score->login]->finalist = false;
            if ($score->score >= $this->pointsLimit) {
                if (!$this->cupScores[$score->login]->hasWin) {
                    $finalists[$login] = $score;
                }
            }
        }
        if (sizeof($finalists) > 0) {
            \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::asortDesc($finalists, "score");
            reset($finalists);
            $score = array_shift($finalists);
            $this->cupScores[$score->login]->finalist = true;
        }

        \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::asortDesc($this->cupScores, "score");

        $this->scoreTable(true, "normal");
    }

    /** at podium */
    public function onEndMatch($rankings, $winnerTeamOrMap) {
        $this->scoreTable(true, "normal");
    }

    /** when map changes */
    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {
        $this->scoreTable(true, "normal");
    }

    public function onBeginMap($map, $warmUp, $matchContinuation) {
        $this->scoreTable(false);
        $this->cupScores = null;
        if ($this->resetData) {
            $this->resetData = false;
            $this->winners = array();
            $this->lastRoundWinner = "";
            $this->roundFinish = array();
        }
        $this->syncScores();
    }

    public function hideUI() {
        \ManiaLive\Gui\CustomUI::HideForAll(\ManiaLive\Gui\CustomUI::SCORETABLE);
    }

    public function Scoretable($show, $layer = "scorestable") {
        Gui\Widgets\Scoretable::EraseAll();
        if ($show) {
            $win = Gui\Widgets\Scoretable::Create();
            \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::asortDesc($this->cupScores, "score");
            $win->populate($this->cupScores, $this->pointsLimit, $this->winners);
            $win->setLayer($layer);
            $win->centerOnScreen();
            // $win->setPosY(30);
            $win->show();
        }
    }

}
