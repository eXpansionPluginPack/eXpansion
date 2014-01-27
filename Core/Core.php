<?php

namespace ManiaLivePlugins\eXpansion\Core;

define('eXp', True);

use ManiaLive\Event\Dispatcher;
use ManiaLive\Utilities\Console;
use ManiaLivePlugins\eXpansion\Core\Events\GameSettingsEvent;
use ManiaLivePlugins\eXpansion\Core\Events\ServerSettingsEvent;

/**
 * Description of Core
 *
 * @author oliverde8
 * @author reaby
 * 
 */
class Core extends types\ExpPlugin {

    const EXP_VERSION = "0.9";

    /**
     * Last used game mode
     * @var \Maniaplanet\DedicatedServer\Structures\GameInfos
     */
    private $lastGameMode;
    private $lastGameSettings;
    private $lastServerSettings;

    /** private variable to hold players infos 
     * @var Structures\ExpPlayer[] */
    private $expPlayers = array();

    /** @var array() */
    private $teamScores = array();

    /**
     * public variable to export player infos 
     * @var Structures\ExpPlayer[] */
    public static $playerInfo = array();

    /** @var string[int] */
    public static $roundFinishOrder = array();

    /** @var string[string][int] */
    public static $checkpointOrder = array();

    /** @var int */
    private $giveupCount = 0;

    /** @var bool $update flag to force calculate player positions */
    private $update = true;

    /** @var bool $enableCalculation marks if player positions should be calculated */
    private $enableCalculation = true;
    private $loopTimer = 0;

    /** @var Config */
    private $config;

    /**
     * 
     */
    function exp_onInit() {
        $logFile = "manialive-" . $this->storage->serverLogin . ".console.log";
        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . $logFile)) {
            unlink(__DIR__ . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . $logFile);
        }
        $logFile = "manialive-" . $this->storage->serverLogin . ".error.log";
        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . $logFile)) {
            unlink(__DIR__ . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . $logFile);
        }
    }

    /**
     * 
     */
    function exp_onLoad() {

        $this->enableDedicatedEvents();
        $config = Config::getInstance();
        i18n::getInstance()->start();
        DataAccess::getInstance()->start();


        $expansion = <<<'EOT'
   
--------------------------------------------------------------------------------   
                     __   __                      _             
                     \ \ / /                     (_)            
                  ___ \ V / _ __   __ _ _ __  ___ _  ___  _ __  
                 / _ \ > < | '_ \ / _` | '_ \/ __| |/ _ \| '_ \ 
                |  __// . \| |_) | (_| | | | \__ \ | (_) | | | |
                 \___/_/ \_\ .__/ \__,_|_| |_|___/_|\___/|_| |_|
                           | |         Plugin Pack for Manialive    
                           |_|                                                              

-------------------------------------------------------------------------------

EOT;

        $this->console($expansion);
        $server = $this->connection->getVersion();
        $d = (object) date_parse_from_format("Y-m-d_H_i", $server->build);
        $this->console('Dedicated Server running for title: ' . $server->titleId);
        $this->console('Dedicated Server build: ' . $d->year . "-" . $d->month . "-" . $d->day);
        $this->connection->setApiVersion($config->API_Version); // For SM && TM
        $this->console('Dedicated Server api version in use: ' . $config->API_Version);
        $this->console('eXpansion version: ' . $this->getVersion());


        $bExitApp = false;

        if (version_compare(PHP_VERSION, '5.3.3') >= 0) {
            $this->console('Minimum PHP version 5.3.3: Pass (' . PHP_VERSION . ')');
        } else {
            $this->console('Minimum PHP version 5.3.3: Fail (' . PHP_VERSION . ')');
            $bExitApp = true;
        }

        if (gc_enabled()) {
            $this->console('Garbage Collector enabled: Pass ');
        } else {
            $this->console('Garbage Collector enabled: Fail )');
            $bExitApp = true;
        }
        $this->console('');
        $this->console('Language support detected for: ' . implode(",", i18n::getInstance()->getSupportedLocales()) . '!');
        $this->console('Enabling default locale: ' . $config->defaultLanguage . '');
        i18n::getInstance()->setDefaultLanguage($config->defaultLanguage);

        $this->console('');
        $this->console('-------------------------------------------------------------------------------');
        $this->console('');
        if (DEBUG) {
            $this->console('                        RUNNING IN DEVELOPMENT MODE  ');
            $this->console('');
            $this->console('-------------------------------------------------------------------------------');
            $this->console('');
        }

        if ($bExitApp) {
            $this->connection->chatSendServerMessage("Failed to init eXpansion, see consolelog for more info!");
            die();
        }

        $this->lastGameMode = \ManiaLive\Data\Storage::getInstance()->gameInfos->gameMode;

        $this->connection->chatSendServerMessage('$fff$w$o$s e $0dfX $fffp a n s i o n');
        $this->connection->chatSendServerMessage('$000$o$iPluginPack for ManiaLive');
        $this->connection->chatSendServerMessage("");
        $this->connection->chatSendServerMessage('$fffRunning with version ' . \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION);
    }

    /**
     * 
     */
    public function exp_onReady() {
        $this->config = Config::getInstance();
        $this->registerChatCommand("server", "showInfo", 0, true);
        $this->registerChatCommand("serverlogin", "serverlogin", 0, true);
        $this->setPublicMethod("showInfo");
        $window = new Gui\Windows\QuitWindow();
        $this->connection->customizeQuitDialog($window->getXml(), "", true, 0);
        $this->onBeginMap(null, null, null);
        $this->resetExpPlayers(true);
        $this->update = true;
        $this->loopTimer = round(microtime(true));

        if ($this->config->enableRanksCalc == true) {
            $this->enableApplicationEvents(\ManiaLive\Application\Event::ON_POST_LOOP);
        } else {
            $this->enableCalculation = false;
        }
    }

    /**
     * Fixes error message on chat command /serverlogin
     * @param type $login
     */
    public function serverlogin($login) {
        
    }

    /**
     * 
     * @param array $map
     * @param bool $warmUp
     * @param bool $matchContinuation
     */
    function onBeginMap($map, $warmUp, $matchContinuation) {

        $gameSettings = \ManiaLive\Data\Storage::getInstance()->gameInfos;
        $newGameMode = $gameSettings->gameMode;

        if ($newGameMode != $this->lastGameMode) {
            Dispatcher::dispatch(new GameSettingsEvent(GameSettingsEvent::ON_GAME_MODE_CHANGE, $this->lastGameMode, $newGameMode));

            $this->lastGameMode = $newGameMode;
            $this->lastGameSettings = clone $gameSettings;

            $this->checkLoadedPlugins();
            $this->checkPluginsOnHold();
        } else {
//Detecting any changes in game Settings
            if ($this->lastGameSettings == null)
                $this->lastGameSettings = clone $gameSettings;
            else {
                $difs = $this->compareObjects($gameSettings, $this->lastGameSettings, array("gameMode", "scriptName"));
                if (!empty($difs)) {
                    Dispatcher::dispatch(new GameSettingsEvent(GameSettingsEvent::ON_GAME_SETTINGS_CHANGE, $this->lastGameSettings, $gameSettings, $difs));
                    $this->lastGameSettings = clone $gameSettings;
                }
            }
        }

//Detecting any changes in Server Settings
        $serverSettings = \ManiaLive\Data\Storage::getInstance()->server;
        if ($this->lastServerSettings == null)
            $this->lastServerSettings = clone $serverSettings;
        else {
            $difs = $this->compareObjects($serverSettings, $this->lastServerSettings);
            if (!empty($difs)) {
                Dispatcher::dispatch(new ServerSettingsEvent(ServerSettingsEvent::ON_SERVER_SETTINGS_CHANGE, $this->lastServerSettings, $serverSettings, $difs));
                $this->lastServerSettings = clone $serverSettings;
            }
        }
        $this->teamScores = array();
    }

    protected function compareObjects($obj1, $obj2, $ingnoreList = array()) {
        $difs = array();

        foreach ($obj1 as $varName => $value) {
            if (!in_array($varName, $ingnoreList))
                if (!isset($obj2->$varName) || $obj2->$varName != $value)
                    $difs[$varName] = true;
        }
        return $difs;
    }

    public function onGameModeChange($oldGameMode, $newGameMode) {

        $this->showNotice("GameMode Changed");
    }

    private function showNotice($message) {
        $this->console('                         _   _       _   _          ');
        $this->console('                        | \ | | ___ | |_(_) ___ ___ ');
        $this->console('                        |  \| |/ _ \| __| |/ __/ _ \ ');
        $this->console('                        | |\  | (_) | |_| | (_|  __/ ');
        $this->console("                        |_| \_|\___/ \__|_|\___\___|");
        $fill = "";
        $firstline = explode("\n", $message, 2);
        if (!is_array($firstline))
            $firstline = array($firstline);
        for ($x = 0; $x < ((80 - strlen($firstline[0])) / 2); $x++) {
            $fill .= " ";
        }
        $this->console($fill . $message);
    }

    private function checkLoadedPlugins() {
        $pHandler = \ManiaLive\PluginHandler\PluginHandler::getInstance();
        $this->console('Shutting down uncompatible plugins');

        foreach ($this->exp_getGameModeCompability() as $plugin => $compability) {
            if (!$plugin::exp_checkGameCompability()) {
                try {
                    $this->callPublicMethod($plugin, 'exp_unload');
                } catch (\Exception $ex) {
                    
                }
            }
        }
    }

    private function checkPluginsOnHold() {
        $this->console('Starting compatible plugins');

        if (!empty(types\BasicPlugin::$plugins_onHold)) {
            $pHandler = \ManiaLive\PluginHandler\PluginHandler::getInstance();
            foreach (types\BasicPlugin::$plugins_onHold as $plugin_id) {
                //$parts = explode("\\", $plugin_id);
                //$className = '\\ManiaLivePlugins\\' . $plugin_id . '\\' . $parts[1];
                $className = $plugin_id;
                if ($className::exp_checkGameCompability() && !$this->isPluginLoaded($plugin_id)) {
                    try {
                        $pHandler->load($plugin_id);
                    } catch (Exception $ex) {
                        $this->console('Plugin : ' . $plugin_id . ' Maybe already loaded');
                    }
                }
            }
        }
    }

    public function showInfo($login) {
        $info = Gui\Windows\InfoWindow::Create($login);
        $info->setTitle("Server info");
        $info->centerOnScreen();
        $info->setSize(120, 90);
        $info->show();
    }

    public function onPostLoop() {
        // check for update conditions
        if ($this->enableCalculation == false)
            return;
        if ($this->storage->serverStatus->code == 4 && $this->update && (microtime(true) - $this->loopTimer) > 0.35) {
            $this->update = false;
            $this->loopTimer = microtime(true);
            $this->calculatePositions();
        }
    }

    public function onPlayerDisconnect($login, $disconnectionReason) {
        $this->update = true;
        if (array_key_exists($login, $this->expPlayers)) {
            $this->expPlayers[$login]->hasRetired = true;
            $this->expPlayers[$login]->isPlaying = false;
            unset($this->expPlayers[$login]);
        }
    }

    public function onPlayerCheckpoint($playerUid, $login, $timeOrScore, $curLap, $checkpointIndex) {
        if ($this->enableCalculation == false)
            return;

        $this->update = true;
        if (!array_key_exists($login, $this->expPlayers)) {
            $player = $this->storage->getPlayerObject($login);
            $this->expPlayers[$login] = Structures\ExpPlayer::fromArray($player->toArray());
        }
        self::$checkpointOrder[$checkpointIndex][] = $login;
        $this->expPlayers[$login]->checkpoints[$checkpointIndex] = $timeOrScore;
        $this->expPlayers[$login]->time = $timeOrScore;
        $this->expPlayers[$login]->curCpIndex = $checkpointIndex;
        $this->expPlayers[$login]->curLap = $curLap;
    }

    function onBeginMatch() {
        $window = new Gui\Windows\QuitWindow();
        $this->connection->customizeQuitDialog($window->getXml(), "", true, 0);
    }

    public function onBeginRound() {
        $this->update = true;
        $this->resetExpPlayers();
    }

    public function onEndRound() {
        $this->update = true;
    }

    public function onPlayerInfoChanged($playerInfo) {
        if ($this->enableCalculation == false)
            return;

        $this->update = true;
        $player = \Maniaplanet\DedicatedServer\Structures\Player::fromArray($playerInfo);

        if (!array_key_exists($player->login, $this->expPlayers)) {
            $login = $player->login;
            $pla = $this->storage->getPlayerObject($player->login);
            if (empty($pla)) {
                return;
            }
            $this->expPlayers[$player->login] = Structures\ExpPlayer::fromArray($pla->toArray());

            if (array_key_exists($login, $this->teamScores))
                $this->expPlayers[$login]->matchScore = $this->teamScores[$login];
            $this->expPlayers[$login]->hasRetired = false;
            $this->expPlayers[$login]->isPlaying = true;
            $this->expPlayers[$login]->checkpoints = array(0 => 0);
            $this->expPlayers[$login]->finalTime = -1;
            $this->expPlayers[$login]->position = -1;
            $this->expPlayers[$login]->time = -1;
            $this->expPlayers[$login]->curCpIndex = -1;
            $this->expPlayers[$login]->isFinished = false;
            // in case player is joining to match in round, he needs to be marked as waiting
            if ($this->storage->gameInfos->gameMode != \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK)
                $this->expPlayers[$login]->isWaiting = true;
        }

        $this->expPlayers[$player->login]->teamId = $player->teamId;
        $this->expPlayers[$player->login]->spectator = $player->spectator;
        $this->expPlayers[$player->login]->temporarySpectator = $player->temporarySpectator;
        $this->expPlayers[$player->login]->pureSpectator = $player->pureSpectator;

        // player just temp spectator
        if ($player->temporarySpectator == true && $player->spectator == false) {
            $this->expPlayers[$player->login]->hasRetired = true;
            $this->expPlayers[$player->login]->isPlaying = true;
            // player is spectator
        } elseif ($player->spectator == true) {
            $this->expPlayers[$player->login]->isPlaying = false;
            $this->expPlayers[$player->login]->hasRetired = true;
        } else {
            // player is not any spectator
            $this->expPlayers[$player->login]->isPlaying = true;
            $this->expPlayers[$player->login]->hasRetired = true;
        }
    }

    public function resetExpPlayers($readRankings = false) {
        self::$roundFinishOrder = array();
        self::$checkpointOrder = array();

        foreach ($this->storage->players as $login => $player) {
            $this->expPlayers[$login] = Structures\ExpPlayer::fromArray($player->toArray());

            if ($player->spectator == 1) {
                $this->expPlayers[$login]->hasRetired = true;
                $this->expPlayers[$login]->isPlaying = false;
                continue;
            }
            if (array_key_exists($login, $this->teamScores))
                $this->expPlayers[$login]->matchScore = $this->teamScores[$login];
            $this->expPlayers[$login]->hasRetired = false;
            $this->expPlayers[$login]->isPlaying = true;
            $this->expPlayers[$login]->checkpoints = array(0 => 0);
            $this->expPlayers[$login]->finalTime = -1;
            $this->expPlayers[$login]->position = -1;
            $this->expPlayers[$login]->time = -1;
            $this->expPlayers[$login]->curCpIndex = -1;
            $this->expPlayers[$login]->isWaiting = false;
            $this->expPlayers[$login]->isFinished = false;
        }


        $rankings = $this->connection->getCurrentRanking(-1, 0);
        foreach ($rankings as $player) {
            if (!empty($player->login) && array_key_exists($player->login, $this->expPlayers)) {
                $this->expPlayers[$player->login]->score = $player->score;
            }
        }
    }

    public function onPlayerFinish($playerUid, $login, $timeOrScore) {
        if ($this->enableCalculation == false)
            return;

// handle onPlayerfinish @ start from server. 
        $this->update = true;
        if ($playerUid == 0)
            return;

        /* if (!array_key_exists($login, $this->expPlayers)) {
          $player = $this->storage->getPlayerObject($login);
          $this->expPlayers[$login] = Structures\ExpPlayer::fromArray($player->toArray());
          } */

        if ($timeOrScore == 0) {
            if (array_key_exists($login, $this->expPlayers)) {
                $this->expPlayers[$login]->finalTime = 0;
                if ($this->storage->gameInfos->gameMode !== \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK) {
                    $this->expPlayers[$login]->hasRetired = true;
                    Dispatcher::dispatch(new Events\PlayerEvent(Events\PlayerEvent::ON_PLAYER_GIVEUP, $this->expPlayers[$login]));
                }
            }
            return;
        }

        if ($timeOrScore > 0) {
            if (array_key_exists($login, $this->expPlayers)) {
                $this->expPlayers[$login]->finalTime = $timeOrScore;
                $this->expPlayers[$login]->isFinished = true;
                if ($this->storage->gameInfos->gameMode !== \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK) {
                    self::$roundFinishOrder[] = $login;
                }
            }

// set points
            if ($this->storage->gameInfos->gameMode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM) {
                $maxpoints = $this->storage->gameInfos->teamMaxPoints;
                $total = 0;
// get total number if players
                foreach ($this->expPlayers as $player) {
                    if ($player->isPlaying)
                        $total++;
                }
// set max points
                if ($total > $maxpoints) {
                    $total = $maxpoints;
                }

                if (array_key_exists($login, $this->expPlayers)) {

                    $player = $this->expPlayers[$login];
                    if ($player->isPlaying) {
                        $points = ($total + 1) - (count(self::$roundFinishOrder));


                        if ($points < 0)
                            $points = 0;

                        if (!array_key_exists($player->login, $this->teamScores)) {
                            $this->teamScores[$player->login] = $points;
                        } else {
                            $this->teamScores[$player->login] += $points;
                        }
                        $this->expPlayers[$player->login]->matchScore = $this->teamScores[$player->login];
                    }
                }
                self::$playerInfo = $this->expPlayers;
            }
        }
    }

    function calculatePositions() {
        /** @var $playerPositions Structures\ExpPlayer[] */
        $playerPositions = array();
        /** @var $playerPositions Structures\ExpPlayer[] */
        $oldExpPlayers = $this->expPlayers;
        $oldGiveupCount = $this->giveupCount;
        $giveupCount = 0;
        $giveupPlayers = array();
        foreach ($this->expPlayers as $login => $player) {
            if (empty($player)) {
                unset($this->expPlayers[$login]);
                continue;
            }

            if ($player->isPlaying == false || $player->isWaiting) {
                unset($this->expPlayers[$login]);
                continue;
            }

            if (isset($player->checkpoints[0])) {
                $player->time = end($player->checkpoints);
// $player->curCpIndex = key($player->checkpoints);
            }
// is player is not playing ie. has become spectator or disconnect, remove...




            if ($player->finalTime == 0) {
                $giveupPlayers[] = $player;
                $this->giveupCount++;
            }
            $playerPositions[] = $player;
        }


        usort($playerPositions, array($this, 'positionCompare'));


        $firstPlayerLogin = null;
        $previousPlayerLogin = null;
        $first = null;
        $previous = null;

        /** @var $playerPositions Structures\ExpPlayer[] */
        foreach ($playerPositions as $pos => $current) {
            $dispatch = false;
            $login = $current->login;
// get old position
            $oldPos = $current->position;
// update new position
            $this->expPlayers[$login]->position = $pos;
            if ($firstPlayerLogin == null) {
                $this->expPlayers[$login]->deltaCpCountTop1 = 0;
                $this->expPlayers[$login]->deltaTimeTop1 = 0;
                $firstPlayerLogin = $login;
            } else {
                $first = $this->expPlayers[$firstPlayerLogin];

                $this->expPlayers[$login]->deltaCpCountTop1 = $first->curCpIndex - $current->curCpIndex - 1;
                if ($this->expPlayers[$login]->deltaCpCountTop1 < 0)
                    $this->expPlayers[$login]->deltaCpCountTop1 = 0;

                $cpindex = $current->curCpIndex;
                if ($cpindex < 0)
                    $cpindex = 0;

                $this->expPlayers[$login]->deltaTimeTop1 = -1;
                if (isset($first->checkpoints[$cpindex]))
                    $this->expPlayers[$login]->deltaTimeTop1 = $current->time - $first->checkpoints[$cpindex];
            }
// reset flags
            $this->expPlayers[$login]->changeFlags = 0;

            if ($pos != $oldPos) {
                $this->expPlayers[$login]->changeFlags |= Structures\ExpPlayer::Player_rank_position_change;
                $dispatch = true;
            }

            if ($oldExpPlayers[$login]->curCpIndex != $current->curCpIndex) {
                $this->expPlayers[$login]->changeFlags |= Structures\ExpPlayer::Player_cp_position_change;
                $dispatch = true;
            }

            if ($dispatch) {
                Dispatcher::dispatch(new Events\PlayerEvent(Events\PlayerEvent::ON_PLAYER_POSITION_CHANGE, $this->expPlayers[$login], $oldPos, $pos));
            }
// set previous player
            if ($previousPlayerLogin == null) {
                $previousPlayerLogin = $login;
                $previous = $current;
            }
        } // end of foreach playerpositions;
// export infos..
        self::$playerInfo = $this->expPlayers;
        \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::asortAsc(self::$playerInfo, "position");
        Dispatcher::dispatch(new Events\PlayerEvent(Events\PlayerEvent::ON_PLAYER_POSITIONS_CALCULATED, self::$playerInfo));
    }

    /** converted from fast.. */
    function positionCompare(Structures\ExpPlayer $a, Structures\ExpPlayer $b) {
// no cp
        if ($a->curCpIndex < 0 && $b->curCpIndex < 0) {
//      echo "no cp";
            return strcmp($a->login, $b->login);
        }
// 2nd have del
        if ($a->finalTime > 0 && $b->finalTime <= 0) {
//    echo "2nd have del";
            return -1;
        }
// 1st have del
        elseif ($a->finalTime <= 0 && $b->finalTime > 0) {
//  echo "1nd have del";
            return 1;
        }
// only 1st
        if ($b->curCpIndex < 0) {
//echo "1st";
            return -1;
        }
// only 2nd
        elseif ($a->curCpIndex < 0) {
//  echo "2nd";
            return 1;
        }
// both ok, so...
        elseif ($a->curCpIndex > $b->curCpIndex) {
//   echo "cp a";
            return -1;
        } elseif ($a->curCpIndex < $b->curCpIndex) {
//       echo "cp b";
            return 1;
        }
// same check, so test time
        elseif ($a->time < $b->time) {
//        echo "time";
            return -1;
        } elseif ($a->time > $b->time) {
//           echo "tiem";
            return 1;
        }
// same check check and time, so test general rank
        elseif ($a->rank == 0 && $b->rank > 0) {
            return 1;
        } elseif ($a->rank > 0 && $b->rank == 0) {
            return -1;
        } elseif ($a->rank < $b->rank) {
            return -1;
        } elseif ($a->rank > $b->rank) {
            return 1;
        }

// same check check, time and rank (only in team or beginning?), so test general scores
        elseif ($a->score > 0 && $b->score > 0 && $a->score > $b->score) {
// echo "score";
            return -1;
        } elseif ($a->score > 0 && $b->score > 0 && $a->score < $b->score) {
// echo "score";
            return 1;
        }
// same check check, time, rank and general score, so test besttime
        elseif ($a->bestTime > 0 && $b->bestTime > 0 && $a->bestTime < $b->bestTime)
            return -1;
        elseif ($a->bestTime > 0 && $b->bestTime > 0 && $a->bestTime > $b->bestTime)
            return 1;
// all same... test time of previous checks
        for ($key = $a->curCpIndex - 1; $key >= 0; $key--) {
            if ($a->checkpoints[$key] < $b->checkpoints[$key])
                return -1;
            elseif ($a->checkpoints[$key] > $b->checkpoints[$key])
                return 1;
        }
// really all same, use login  :p
//    echo "use login";
        return strcmp($a->login, $b->login);
    }

}

?>
