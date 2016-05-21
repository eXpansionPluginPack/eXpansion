<?php

namespace ManiaLivePlugins\eXpansion\ESLcup;

/**
 * ESLcup
 *
 * Purpose of this plugin is to enhance the native cupmode to comply with ESL rules
 *
 *
 * @author Reaby
 */
class ESLcup extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    /** @var \ManiaLivePlugins\eXpansion\ESLcup\Structures\CupScore[] $cupScores holds the cup scores, sorted by greatest score */
    private $cupScores = array();

    /** @var string $lastRoundWinner contains the login of last round winner */
    private $lastRoundWinner = "";

    /** @var int $pointsLimit used to mark the pointslimit of the mode */
    private $pointsLimit = 100;

    /** @var bool $resetData used to flag if scores needs to be reset at onBebinMap */
    private $resetData = false;

    /** @var bool $enabled used to flag if mode is enabled */
    private $enabled = false;

    /** @var \ManiaLive\Data\Player[] holds the players who finished this round in order of arrival */
    private $roundFinish = array();

    /** @var \ManiaLivePlugins\eXpansion\ESLcup\Structures\CupScore[] holds winners in order of arrival */
    private $winners = array();

    /**
     * onload
     */
    public function expOnInit()
    {
        $this->exp_addTitleSupport("TM");
        $this->exp_addTitleSupport("Trackmania");
        $this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP);

    }

    public function eXpOnLoad()
    {
        $this->enableDedicatedEvents();
        $this->setPublicMethod("syncScores");
        //$this->registerChatCommand("test", "testData", 1, true);
        //$this->registerChatCommand("specrel", "releaseSpec", 0, false);

        $admingroup = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance();
        $cmd = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::addAdminCommand('game eslcup', $this, 'chat_eslcup', 'game_settings');
        $admingroup->addShortAlias($cmd, 'eslcup');
    }

    /**
     * onReady
     *
     */
    public function eXpOnReady()
    {
        if (!$this->enabled)
            return;

        $this->syncScores();
        $this->hideUI();
        $this->scoreTable(true, "scorestable");
    }

    public function check_gameSettings_Cup()
    {
        return $this->connection->getNextGameInfo()->gameMode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP;
    }

    public function check_eslCup()
    {
        return $this->enabled;
    }

    function chat_eslcup($fromLogin, $params)
    {

        try {
            $command = array_shift($params);

            switch (strtolower($command)) {
                case "start":
                    $this->winners = array();
                    $this->roundFinish = array();
                    $this->lastRoundWinner = "";
                    $this->enabled = true;
                    $this->resetData = true;
                    $params = $params[0];
                    if (is_numeric($params)) {
                        $this->pointsLimit = $params;
                        $newlimit = (intval($params) * 2) + 500;
                        $this->connection->setCupPointsLimit($newlimit);
                    }
                    $this->eXpChatSendServerMessage("Starting ESLcup with point limit: " . $this->pointsLimit);
                    $this->connection->nextMap(false);
                    break;
                case "stop":
                    $this->showUI();
                    $this->cupScores = array();
                    $this->winners = array();
                    $this->roundFinish = array();
                    $this->lastRoundWinner = "";
                    $this->Scoretable(false);
                    $this->enabled = false;
                    $this->releaseSpec();
                    $this->connection->setCupPointsLimit(100);
                    $this->eXpChatSendServerMessage("ESLcup disabled, normal cup point limit set to 100");
                    $this->connection->nextMap(false);
                    break;
                case "pointslimit":
                case "limit":
                    $params = $params[0];
                    if (is_numeric($params)) {
                        $this->pointsLimit = $params;
                        $this->eXpChatSendServerMessage("ESLcup points limit is now set to:" . $params);
                        $newlimit = (intval($params) * 2) + 500;
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
                    $this->eXpChatSendServerMessage("command not found", $fromLogin);
                    break;
            }
        } catch (\Exception $e) {

        }
    }

    /**
     * SyncScores from player rankings
     */
    public function syncScores()
    {
        if (!$this->enabled)
            return;
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

        // populate testData

        $this->findFinalists();

        $this->Scoretable(true, "scorestable");
    }

    function testData($login, $nb)
    {
        if (!$this->enabled)
            return;
        $this->syncScores();

        if (array_key_exists($nb, $this->storage->players)) {
            $this->cupScores[$nb]->hasWin = time();
            $this->winners[0] = $this->cupScores[$nb];
        }
        /* for ($x = 0; $x < intval($nb); $x++) {
          $player = new \Maniaplanet\DedicatedServer\Structures\Player();
          $player->playerId = 0;
          $player->login = "fakeplayer" . $x;
          $player->nickName = "fakeplayer" . $x;
          $player->score = rand(0, 80);
          $this->cupScores[$player->login] = new Structures\CupScore($player->playerId, $player->login, $player->nickName, $player->score);
          } */

        $this->findFinalists();

        $nbWinners = 1;
        if (count($this->cupScores) > 2) {
            $nbWinners = 2;
        }
        if (count($this->cupScores) > 3) {
            $nbWinners = 3;
        }
        Helper::log("[ESLcup]Nb Winners : $nbWinners");
        $this->Scoretable(true);
    }

    public function onBeginRound()
    {
        if (!$this->enabled)
            return;
        $this->roundFinish = array();
        $this->scoreTable(true, "scorestable");
    }

    public function onPlayerConnect($login, $isSpec)
    {
        if (!$this->enabled)
            return;
        $player = $this->storage->getPlayerObject($login);
        if (!array_key_exists($player->login, $this->cupScores)) {
            $this->cupScores[$player->login] = new Structures\CupScore($player->playerId, $player->login, $player->nickName, $player->score);
            $this->cupScores[$player->login]->isConnected = true;
        } else {
            $this->cupScores[$player->login]->isConnected = true;
        }

        foreach ($this->winners as $winner) {
            if ($winner->login == $login) {
                $this->connection->forceSpectator($login, 1);
                break;
            }
        }
        $this->Scoretable(true, "scorestable");
    }

    public function onPlayerDisconnect($login, $disconnectionReason = null)
    {
        if (!$this->enabled)
            return;

        if (array_key_exists($login, $this->cupScores)) {
            $this->cupScores[$login]->isConnected = false;
        }
        $this->Scoretable(true, "scorestable");
    }

    /**
     *
     *
     * @param int $playerUid
     * @param string $login
     * @param int $timeOrScore
     *
     * @return null
     */
    public function onPlayerFinish($playerUid, $login, $timeOrScore)
    {
        if (!$this->enabled)
            return;
        if ($timeOrScore == 0)
            return;

        $player = new \ManiaLive\Data\Player();
        $player->playerId = $playerUid;
        $player->login = $login;
        $player->nickName = $this->storage->getPlayerObject($login)->nickName;
        $player->rank = count($this->roundFinish) + 1;
        $this->roundFinish[] = $player;
    }

    /**
     *
     * @return null
     */
    public function onEndRound()
    {
        if (!$this->enabled)
            return;
        $scores = $this->connection->getRoundCustomPoints();

        if (sizeof($scores) < 1) {
            $scores = array(10, 6, 4, 3, 2, 1);
        }

        foreach ($this->roundFinish as $ranking => $player) {
            if (!array_key_exists($player->login, $this->cupScores)) {
                $scores[$player->login] = new Structures\CupScore($player->playerId, $player->login, $player->nickName, $player->score);
            } else {
                $points = 1;
                if (isset($scores[($player->rank - 1)])) {
                    $points = $scores[($player->rank - 1)];
                }
                $this->cupScores[$player->login]->score += $points;

                // check for end conditions, so if player is finalist
                if ($this->cupScores[$player->login]->finalist) {

                    //if ($this->lastRoundWinner == $player->login) {
                    // and has won the last round
                    if ($ranking == 0) {
                        // mark his as a winner
                        $this->cupScores[$player->login]->hasWin = time();
                        $this->winners[] = $this->cupScores[$player->login];
                        // send message about win
                        $this->eXpChatSendServerMessage($player->nickName . ' $z$s$fff takes the ' . count($this->winners) . ' place!');
                        $this->connection->forceSpectator($player->login, 1);
                        // check if there is need for more winners
                        $nbWinners = 1;
                        if (count($this->storage->players) > 2) {
                            $nbWinners = 2;
                        }
                        if (count($this->storage->players) > 3) {
                            $nbWinners = 3;
                        }

                        if (count($this->winners) >= $nbWinners) {
                            $this->scoreTable(true, "normal");

                            $this->eXpChatSendServerMessage("          ESL CUP RESULTS         ");
                            $this->eXpChatSendServerMessage("**********************************");
                            foreach ($this->winners as $i => $winner) {
                                $this->eXpChatSendServerMessage(($i + 1) . ". place " . $winner->nickName);
                            }
                            $this->eXpChatSendServerMessage("**********************************");
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
        $this->findFinalists();
        $this->scoreTable(true, "normal");
    }

    public function findFinalists()
    {
        if (!$this->enabled)
            return;

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
            $highestScore = -1;
            foreach ($finalists as $item) {
                if ($highestScore == -1) {
                    $highestScore = $item->score;
                    $this->cupScores[$item->login]->finalist = true;
                    foreach ($this->winners as $winner) {
                        if ($winner->login == $item->login) {
                            $this->cupScores[$item->login]->finalist = false;
                            $highestScore = -1;
                            continue;
                        }
                    }
                    continue;
                }

                if ($item->score == $highestScore) {
                    $highestScore = $item->score;
                    $this->cupScores[$item->login]->finalist = true;
                    foreach ($this->winners as $winner) {
                        if ($winner->login == $item->login) {
                            $this->cupScores[$item->login]->finalist = false;
                            continue;
                        }
                    }
                }
            }
        }

        \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::asortDesc($this->cupScores, "score");
    }

    /** at podium */
    public function onEndMatch($rankings, $winnerTeamOrMap)
    {
        if (!$this->enabled)
            return;
        $this->scoreTable(true, "normal");
    }

    /** when map changes */
    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap)
    {
        if (!$this->enabled)
            return;
        $this->scoreTable(true, "normal");
    }

    public function onBeginMap($map, $warmUp, $matchContinuation)
    {
        if (!$this->enabled)
            return;

        if ($this->resetData) {
            $this->resetData = false;
            $this->doReset();
        }
        $this->scoreTable(false);
        $this->syncScores();
    }

    public function doReset()
    {
        $this->resetData = false;
        $rankings = $this->connection->getCurrentRanking(-1, 0);
        $out = array();
        foreach ($rankings as $player) {
            $out[] = array("PlayerId" => intval($player->playerId), "Score" => 0);
        }
        $this->connection->forceScores($out, true);

        foreach ($this->storage->spectators as $login => $player) {
            if ($player->forceSpectator) {
                Helper::log("[ESLcup]Releasing spactator : $login");
                $this->connection->forceSpectator($login, 2);
                $this->connection->forceSpectator($login, 0);
            }
        }
        $this->cupScores = null;
        $this->winners = array();
        $this->lastRoundWinner = "";
        $this->roundFinish = array();
    }

    public function releaseSpec()
    {

        foreach ($this->storage->spectators as $login => $player) {
            if ($player->forceSpectator) {
                Helper::log("[ESLcup]Realesing spectator : $login");
                $this->connection->forceSpectator($login, 2);
                $this->connection->forceSpectator($login, 0);
            }
        }
    }

    public function hideUI()
    {
        \ManiaLive\Gui\CustomUI::HideForAll(\ManiaLive\Gui\CustomUI::SCORETABLE);
    }

    public function showUI()
    {
        \ManiaLive\Gui\CustomUI::ShowForAll(\ManiaLive\Gui\CustomUI::SCORETABLE);
    }

    public function Scoretable($show, $layer = "scorestable")
    {
        if (!$this->enabled)
            return;
        $this->hideUI();
        Gui\Widgets\Scoretable::EraseAll();
        if ($show) {
            foreach ($this->storage->players as $login => $player) {
                $this->showScoretable($login, $layer);
            }
            foreach ($this->storage->spectators as $login => $player) {
                $this->showScoretable($login, $layer);
            }
        }
    }

    public function showScoretable($login, $layer)
    {
        if (!$this->enabled)
            return;
        $win = Gui\Widgets\Scoretable::Create($login);
        \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::asortDesc($this->cupScores, "score");
        $win->setData($this->cupScores, $this->pointsLimit, $this->winners);
        $win->setLayer($layer);
        $win->setPosZ(180);
        $win->centerOnScreen();
        $win->show();
    }

    public function eXpOnUnload()
    {
        $this->enabled = false;
        $this->winners = array();
        $this->roundFinish = array();
        $this->lastRoundWinner = "";
        $this->resetData = false;
    }

}
