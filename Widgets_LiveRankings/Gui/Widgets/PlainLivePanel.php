<?php

namespace ManiaLivePlugins\eXpansion\Widgets_LiveRankings\Gui\Widgets;

use Exception;
use ManiaLib\Utils\Formatting;
use ManiaLivePlugins\eXpansion\Core\Core;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\Widgets_LiveRankings\Gui\Scripts\CpPositions;
use ManiaLivePlugins\eXpansion\Widgets_LiveRankings\Widgets_LiveRankings;
use ManiaLivePlugins\eXpansion\Widgets_LocalRecords\Gui\Controls\Recorditem;
use ManiaLivePlugins\eXpansion\Widgets_LocalRecords\Gui\Controls\TeamItem;
use ManiaLivePlugins\eXpansion\Widgets_LocalRecords\Gui\Widgets\PlainPanel;
use ManiaLivePlugins\eXpansion\Widgets_LocalRecords\Widgets_LocalRecords;
use Maniaplanet\DedicatedServer\Structures\GameInfos;

class PlainLivePanel extends PlainPanel
{

    public static $connection;

    function exp_onBeginConstruct()
    {
        parent::exp_onBeginConstruct();
        $this->setName("Live Rankings Panel");
        $this->timeScript->setParam('varName', 'LiveTime1');
        $this->timeScript->setParam('getCurrentTimes', 'True');
    }

    protected function getScript()
    {
        $gm = Widgets_LiveRankings::exp_getCurrentCompatibilityGameMode();
        if ($gm == GameInfos::GAMEMODE_ROUNDS || $gm == GameInfos::GAMEMODE_CUP || $gm == GameInfos::GAMEMODE_TEAM || Widgets_LocalRecords::exp_getCurrentCompatibilityGameMode(
            ) == GameInfos::GAMEMODE_LAPS
        ) {

            $script           = new CpPositions();
            $this->timeScript = $script;
            $this->timeScript->setParam("totalCp", $this->storage->currentMap->nbCheckpoints);
            $this->timeScript->setParam("nbFields", 20);
            $this->timeScript->setParam("nbFirstFields", 5);
            $this->timeScript->setParam('varName', 'LiveTime1');
            $this->timeScript->setParam("playerTimes", 'Integer[Text][Integer]');
            $this->timeScript->setParam("nickNames", 'Text[Text][Integer]');
            $this->timeScript->setParam("bestCps", 'Integer[Integer]');
            $this->timeScript->setParam("maxCp", -1);

            $this->timeScript->setParam("givePoints", "True");
            $this->timeScript->setParam("points", "Integer[Integer]");
            $this->timeScript->setParam("nbLaps", 1);
            $this->timeScript->setParam("isLaps", "False");
            $this->timeScript->setParam("isTeam", "False");
            $this->timeScript->setParam("playerTeams", "Integer[Text]");


            $teamMaxPoint = 10;
            if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_SCRIPT) {
                $settings = self::$connection->getModeScriptSettings();
                if (array_key_exists("S_ForceLapsNb", $settings)) {
                    $nbLaps = $settings['S_ForceLapsNb'] == -1 ? 1 : $settings['S_ForceLapsNb'];
                }
                if (isset($settings['S_MaxPointsPerRound'])) {
                    $teamMaxPoint = $settings['S_MaxPointsPerRound'];
                }
            } else {
                $teamMaxPoint = $this->storage->gameInfos->teamPointsLimit;
            }

            $this->timeScript->setParam("maxPoint", $teamMaxPoint);

            if (Widgets_LocalRecords::exp_getCurrentCompatibilityGameMode() == GameInfos::GAMEMODE_LAPS) {
                $this->timeScript->setParam("isLaps", "True");

                if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_SCRIPT)
                    $this->timeScript->setParam("nbLaps", $nbLaps);
                else
                    $this->timeScript->setParam("nbLaps", $this->storage->gameInfos->lapsNbLaps);
            } else if (Widgets_LocalRecords::exp_getCurrentCompatibilityGameMode(
                ) == GameInfos::GAMEMODE_ROUNDS && $this->storage->gameInfos->roundsForcedLaps > 0
            ) {
                $this->timeScript->setParam("isLaps", "True");

                if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_SCRIPT)
                    $this->timeScript->setParam("nbLaps", $nbLaps);
                else
                    $this->timeScript->setParam("nbLaps", $this->storage->gameInfos->roundsForcedLaps);
            } else if (Widgets_LocalRecords::exp_getCurrentCompatibilityGameMode(
                ) == GameInfos::GAMEMODE_TEAM && $this->storage->gameInfos->roundsForcedLaps > 0
            ) {
                $this->timeScript->setParam("isLaps", "True");

                if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_SCRIPT)
                    $this->timeScript->setParam("nbLaps", $$nbLaps);
                else
                    $this->timeScript->setParam("nbLaps", $this->storage->gameInfos->roundsForcedLaps);
            } else if (Widgets_LocalRecords::exp_getCurrentCompatibilityGameMode(
                ) == GameInfos::GAMEMODE_CUP && $this->storage->gameInfos->roundsForcedLaps > 0
            ) {
                $this->timeScript->setParam("isLaps", "True");

                if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_SCRIPT)
                    $this->timeScript->setParam("nbLaps", $nbLaps);
                else
                    $this->timeScript->setParam("nbLaps", $this->storage->gameInfos->roundsForcedLaps);
            }

            if (Widgets_LocalRecords::exp_getCurrentCompatibilityGameMode() == GameInfos::GAMEMODE_LAPS) {
                $this->timeScript->setParam("givePoints", "False");
            }

            if (Widgets_LocalRecords::exp_getCurrentCompatibilityGameMode() == GameInfos::GAMEMODE_TEAM) {
                $this->timeScript->setParam("isTeam", "True");
            }

            if (!empty(Widgets_LiveRankings::$roundPoints)) {
                $this->timeScript->setParam("points", "[" . implode(",", Widgets_LiveRankings::$roundPoints) . "]");
            }

            return $script;
        } else {
            return parent::getScript();
        }
    }

    public function setNbFields($nb)
    {
        if (Widgets_LocalRecords::exp_getCurrentCompatibilityGameMode() == GameInfos::GAMEMODE_TEAM)
            parent::setNbFields($nb - 1);
        else
            parent::setNbFields($nb);
    }

    function update()
    {

        $login = $this->getRecipient();
        foreach ($this->items as $item)
            $item->destroy();

        $this->items = array();
        $this->frame->clearComponents();

        $index = 1;

        $this->bgTitle->setText('Live Rankings');


        $recsData = "";
        $nickData = "";

        $gm    = Widgets_LiveRankings::exp_getCurrentCompatibilityGameMode();
        $short = false;
        if ($gm == GameInfos::GAMEMODE_ROUNDS || $gm == GameInfos::GAMEMODE_CUP || $gm == GameInfos::GAMEMODE_TEAM || $gm == GameInfos::GAMEMODE_LAPS) {
            $short = true;
        }

        for ($index = 1; $index <= $this->nbFields; $index++) {
            $this->items[$index - 1] = new Recorditem($index, false, $short);
            $this->frame->addComponent($this->items[$index - 1]);
        }

        if ($gm == GameInfos::GAMEMODE_TEAM) {
            $this->items[$index - 1] = new TeamItem();
            $this->frame->addComponent($this->items[$index - 1]);
        }

        if ($gm == GameInfos::GAMEMODE_ROUNDS || $gm == GameInfos::GAMEMODE_CUP || $gm == GameInfos::GAMEMODE_TEAM || $gm == GameInfos::GAMEMODE_LAPS) {
            $this->cpUpdate();
        } else {
            $this->taUpdate();
        }
    }

    protected function taUpdate()
    {


        $index = 1;

        try {
            $players = self::$connection->getCurrentRanking(100, 0);
        } catch (Exception $ex) {
            $players = array();
        }
        $recsData = "";
        $nickData = "";

        foreach ($players as $player) {
            if (!empty($player->bestTime) && $player->bestTime > 0) {
                if ($index > 1) {
                    $recsData .= ', ';
                    $nickData .= ', ';
                }
                $recsData .= '"' . Gui::fixString($player->login) . '"=>' . $player->bestTime;
                $nickData .= '"' . Gui::fixString($player->login) . '"=>"' . Gui::fixString(
                        Formatting::stripColors($player->nickName)
                    ) . '"';
                $index++;
            }
        }

        $this->timeScript->setParam("totalCp", $this->storage->currentMap->nbCheckpoints);

        if (empty($recsData)) {
            $recsData = 'Integer[Text]';
            $nickData = 'Text[Text]';
        } else {
            $recsData = '[' . $recsData . ']';
            $nickData = '[' . $nickData . ']';
            $bestCps  = implode(',', $bestCps);
        }


        /* $recsData = 'Integer[Text]';
          $nickData = 'Text[Text]';
         */
        $this->timeScript->setParam("playerTimes", $recsData);
        $this->timeScript->setParam("playerNicks", $nickData);
    }

    protected function cpUpdate()
    {
        if (!Widgets_LiveRankings::$raceOn) {
            return;
        }


        $nbCheckpoints   = array();
        $playerCps       = array();
        $playerNickNames = array();
        $bestCps         = array();
        $biggestCp       = -1;

        foreach (Core::$playerInfo as $login => $player) {
            $lastCpIndex = count($player->checkpoints) - 1;
            if ($player->isPlaying && $lastCpIndex >= 0 && isset($player->checkpoints[$lastCpIndex]) && $player->checkpoints[$lastCpIndex] > 0) {

                if ($lastCpIndex > $biggestCp)
                    $biggestCp = $lastCpIndex;

                $lastCpTime                                    = $player->checkpoints[$lastCpIndex];
                $player                                        = $this->storage->getPlayerObject($login);
                $playerCps[$lastCpIndex][$login]               = $lastCpTime;
                $playerNickNames[$lastCpIndex][$player->login] = Formatting::stripColors($player->nickName);
                $playerTeams[$login]                           = $player->teamId;
            }
        }

        $newPlayerCps = array();
        foreach ($playerCps as $coIndex => $cpsTimes) {
            arsort($cpsTimes);
            $newPlayerCps[$coIndex] = $cpsTimes;
        }

        $playerTimes = "[";
        $NickNames   = "[";
        $teams       = "[";

        $cpCount  = 0;
        $teamCont = 0;
        foreach ($newPlayerCps as $cpIndex => $cpTimes) {
            if ($cpCount != 0) {
                $playerTimes .= ", ";
                $NickNames .= ", ";
            }
            $playerTimes .= $cpIndex . "=>[";
            $NickNames .= $cpIndex . "=>[";

            $cCount                  = 0;
            $nbCheckpoints[$cpIndex] = 0;
            foreach ($cpTimes as $login => $score) {
                if ($cCount != 0) {
                    $playerTimes .= ", ";
                    $NickNames .= ", ";
                }
                if ($teamCont != 0) {
                    $teams .= ", ";
                }
                $playerTimes .= '"' . $login . "\"=>" . $score;
                $NickNames .= '"' . $login . "\"=>\"" . Gui::fixString($playerNickNames[$cpIndex][$login]) . "\"";
                $teams .= '"' . $login . "\"=>" . ($playerTeams[$login] == 1 ? 0 : 1);
                $nbCheckpoints[$cpIndex]++;
                $cCount++;
                $teamCont++;

                if (!isset($bestCps[$cpIndex]) || $score < $bestCps[$cpIndex]) {
                    $bestCps[$cpIndex] = $score;
                }
            }

            $playerTimes .= "]";
            $NickNames .= "]";
            $cpCount++;

        }
        $playerTimes .= "]";
        $NickNames .= "]";
        $teams .= "]";

        $bestCpsText = '';
        foreach($bestCps as $cpIndex=>$time){
            if($bestCpsText != "")
                $bestCpsText .=', ';
            $bestCpsText .= $cpIndex .'=>'. $time;
        }

        $bestCps  = '['.$bestCpsText.']';


        if ($teamCont == 0) {
            $this->timeScript->setParam("playerTeams", "Integer[Text]");
        } else
            $this->timeScript->setParam("playerTeams", $teams);

        if (!empty($newPlayerCps)) {
            $this->timeScript->setParam("playerTimes", $playerTimes);
            $this->timeScript->setParam("nickNames", $NickNames);
            $this->timeScript->setParam("maxCp", $biggestCp + 1);
            $this->timeScript->setParam("bestCps", $bestCps);
        } else {
            $this->timeScript->setParam("playerTimes", 'Integer[Text][Integer]');
            $this->timeScript->setParam("nickNames", 'Text[Text][Integer]');
            $this->timeScript->setParam("maxCp", -1);
        }
    }

}

?>
