<?php

namespace ManiaLivePlugins\eXpansion\Widgets_AroundMe\Gui\Widgets;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\UIConstructionSimple_Buttons;
use ManiaLib\Utils\Formatting;
use ManiaLive\Data\Storage;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Core\Core;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Widget;
use ManiaLivePlugins\eXpansion\Widgets_AroundMe\Gui\Scripts\CpPositions;
use ManiaLivePlugins\eXpansion\Widgets_AroundMe\Widgets_AroundMe;
use ManiaLivePlugins\eXpansion\Widgets_LiveRankings\Widgets_LiveRankings;
use ManiaLivePlugins\eXpansion\Widgets_LocalRecords\Widgets_LocalRecords;
use Maniaplanet\DedicatedServer\Structures\GameInfos;

class AroundMe extends Widget
{

    protected $storage;

    public static $connection;

    public function eXpOnBeginConstruct()
    {
        $this->storage = Storage::getInstance();

        $this->setScriptEvents();
        $this->registerScript($this->getScript());

        $this->setName("Around Me Panel");
        $this->timeScript->setParam('varName', 'aroundMe');
        $this->timeScript->setParam('getCurrentTimes', 'True');

        $this->_windowFrame = new Frame();
        $this->_windowFrame->setAlign("left", "top");
        $this->_windowFrame->setId("Frame");
        $this->_windowFrame->setScriptEvents(true);
        $this->addComponent($this->_windowFrame);

        $icon = new UIConstructionSimple_Buttons();
        $icon->setSubStyle(UIConstructionSimple_Buttons::Drive);
        $icon->setSize(30, 25);
        $icon->setPositionX(-1 * $this->getSizeX());
        $icon->setPositionY(8);
        $this->_windowFrame->addComponent($icon);

        $behindLabel = new Label();
        $behindLabel->setStyle('TextRaceChronoError');
        $behindLabel->setText('$FFF');
        $behindLabel->setId('behindLabel');
        $behindLabel->setPositionX(-20);
        $this->_windowFrame->addComponent($behindLabel);

        $frontLabel = new Label();
        $frontLabel->setStyle('TextRaceChronoError');
        $frontLabel->setText('$FFF');
        $frontLabel->setId('frontLabel');
        $frontLabel->setPositionX(28);
        $this->_windowFrame->addComponent($frontLabel);
    }

    protected function getScript()
    {
        $gm = Widgets_LiveRankings::eXpGetCurrentCompatibilityGameMode();

        $script = new CpPositions();
        $this->timeScript = $script;
        $this->timeScript->setParam("totalCp", Storage::getInstance()->currentMap->nbCheckpoints);
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

        if (Widgets_LocalRecords::eXpGetCurrentCompatibilityGameMode() == GameInfos::GAMEMODE_LAPS) {
            $this->timeScript->setParam("isLaps", "True");

            if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_SCRIPT)
                $this->timeScript->setParam("nbLaps", $nbLaps);
            else
                $this->timeScript->setParam("nbLaps", $this->storage->gameInfos->lapsNbLaps);
        } else if (Widgets_LocalRecords::eXpGetCurrentCompatibilityGameMode() == GameInfos::GAMEMODE_ROUNDS && $this->storage->gameInfos->roundsForcedLaps > 0
        ) {
            $this->timeScript->setParam("isLaps", "True");

            if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_SCRIPT)
                $this->timeScript->setParam("nbLaps", $nbLaps);
            else
                $this->timeScript->setParam("nbLaps", $this->storage->gameInfos->roundsForcedLaps);
        } else if (Widgets_LocalRecords::eXpGetCurrentCompatibilityGameMode() == GameInfos::GAMEMODE_TEAM && $this->storage->gameInfos->roundsForcedLaps > 0
        ) {
            $this->timeScript->setParam("isLaps", "True");

            if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_SCRIPT)
                $this->timeScript->setParam("nbLaps", $$nbLaps);
            else
                $this->timeScript->setParam("nbLaps", $this->storage->gameInfos->roundsForcedLaps);
        } else if (Widgets_LocalRecords::eXpGetCurrentCompatibilityGameMode() == GameInfos::GAMEMODE_CUP && $this->storage->gameInfos->roundsForcedLaps > 0
        ) {
            $this->timeScript->setParam("isLaps", "True");

            if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_SCRIPT)
                $this->timeScript->setParam("nbLaps", $nbLaps);
            else
                $this->timeScript->setParam("nbLaps", $this->storage->gameInfos->roundsForcedLaps);
        }

        if (Widgets_LocalRecords::eXpGetCurrentCompatibilityGameMode() == GameInfos::GAMEMODE_TEAM) {
            $this->timeScript->setParam("isTeam", "True");
        }

        return $script;
    }

    public function update()
    {


        $this->cpUpdate();

    }


    protected function cpUpdate()
    {
        if (!Widgets_AroundMe::$raceOn) {
            return;
        }

        $nbCheckpoints = array();
        $playerCps = array();
        $playerNickNames = array();
        $bestCps = array();
        $biggestCp = -1;

        foreach (Core::$playerInfo as $login => $player) {
            $lastCpIndex = count($player->checkpoints) - 1;
            if ($player->isPlaying && $lastCpIndex >= 0 && isset($player->checkpoints[$lastCpIndex]) && $player->checkpoints[$lastCpIndex] > 0) {

                if ($lastCpIndex > $biggestCp)
                    $biggestCp = $lastCpIndex;

                $lastCpTime = $player->checkpoints[$lastCpIndex];
                $player = $this->storage->getPlayerObject($login);
                $playerCps[$lastCpIndex][$login] = $lastCpTime;
                $playerNickNames[$lastCpIndex][$player->login] = Formatting::stripColors($player->nickName);
                $playerTeams[$login] = $player->teamId;
            }
        }

        $newPlayerCps = array();
        foreach ($playerCps as $coIndex => $cpsTimes) {
            arsort($cpsTimes);
            $newPlayerCps[$coIndex] = $cpsTimes;
        }

        $playerTimes = "[";
        $NickNames = "[";
        $teams = "[";

        $cpCount = 0;
        $teamCont = 0;
        foreach ($newPlayerCps as $cpIndex => $cpTimes) {
            if ($cpCount != 0) {
                $playerTimes .= ", ";
                $NickNames .= ", ";
            }
            $playerTimes .= $cpIndex . "=>[";
            $NickNames .= $cpIndex . "=>[";

            $cCount = 0;
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
        foreach ($bestCps as $cpIndex => $time) {
            if ($bestCpsText != "")
                $bestCpsText .= ', ';
            $bestCpsText .= $cpIndex . '=>' . $time;
        }

        $bestCps = '[' . $bestCpsText . ']';


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
