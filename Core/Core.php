<?php

namespace ManiaLivePlugins\eXpansion\Core;

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

    /**
     * Last used game mode
     * @var \DedicatedApi\Structures\GameInfos
     */
    private $lastGameMode;
    private $lastGameSettings;
    private $lastServerSettings;

    /** private variable to hold players infos 
     * @var Structures\ExpPlayer[] */
    private $expPlayers = array();

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
    private $loopTimer = 0;

    /**
     * 
     */
    function exp_onInit() {
        parent::exp_onInit();
    }

    /**
     * 
     */
    function exp_onLoad() {

        $this->enableDedicatedEvents();

        $this->connection->chatSendServerMessage('$fffStarting e$a00X$fffpansion v. ' . $this->getVersion());
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

        Console::println($expansion);
        $server = $this->connection->getVersion();
        $d = (object) date_parse_from_format("Y-m-d_H_i", $server->build);
        Console::println('Dedicated Server running for title: ' . $server->titleId);
        Console::println('Dedicated Server build: ' . $d->year . "-" . $d->month . "-" . $d->day);
        $this->connection->setApiVersion($config->API_Version); // For SM && TM
        Console::println('Dedicated Server api version in use: ' . $config->API_Version);
        Console::println('eXpansion version: ' . $this->getVersion());


        $bExitApp = false;

        if (version_compare(PHP_VERSION, '5.3.3') >= 0) {
            Console::println('Minimum PHP version 5.3.3: Pass (' . PHP_VERSION . ')');
        } else {
            Console::println('Minimum PHP version 5.3.3: Fail (' . PHP_VERSION . ')');
            $bExitApp = true;
        }

        if (gc_enabled()) {
            Console::println('Garbage Collector enabled: Pass ');
        } else {
            Console::println('Garbage Collector enabled: Fail )');
            $bExitApp = true;
        }
        Console::println('');
        Console::println('Language support detected for: ' . implode(",", i18n::getInstance()->getSupportedLocales()) . '!');
        Console::println('Enabling default locale: ' . $config->defaultLanguage . '');
        i18n::getInstance()->setDefaultLanguage($config->defaultLanguage);

        Console::println('');
        Console::println('-------------------------------------------------------------------------------');
        Console::println('');
        if (DEBUG) {
            Console::println('                        RUNNING IN DEVELOPMENT MODE  ');
            Console::println('');
            Console::println('-------------------------------------------------------------------------------');
            Console::println('');
        }

        if ($bExitApp) {
            $this->connection->chatSendServerMessage("Failed to init eXpansion, see consolelog for more info!");
            die();
        }

        $this->lastGameMode = \ManiaLive\Data\Storage::getInstance()->gameInfos->gameMode;
    }

    /**
     * 
     */
    public function exp_onReady() {
        //  $rankings = $this->connection->getCurrentRanking(-1, 0);
        //  foreach ($rankings as $player) {
        //      $this->expPlayers[$player->login] = Structures\ExpPlayer::fromArray($player->toArray());
        //  }
        $this->registerChatCommand("info", "showInfo", 0, true);
        $this->registerChatCommand("serverlogin", "serverlogin", 0, true);
        $window = new Gui\Windows\QuitWindow();
        $this->connection->customizeQuitDialog($window->getXml(), "", true, 0);
        $this->onBeginMap(null, null, null);
        $this->loopTimer = round(microtime(true));
        $this->enableApplicationEvents(\ManiaLive\Application\Event::ON_POST_LOOP);
    }

    /**
     * Fixes error message on chat command /serverlogin
     * @param type $login
     */
    public function serverlogin($login) {
        
    }

    public function onGameSettingsChange(\DedicatedApi\Structures\GameInfos $oldSettings, \DedicatedApi\Structures\GameInfos $newSettings, $changes) {
        $window = new Gui\Windows\QuitWindow();
        $this->connection->customizeQuitDialog($window->getXml(), "", true, 0);
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

    private function checkLoadedPlugins() {
        $pHandler = \ManiaLive\PluginHandler\PluginHandler::getInstance();
        Console::println('#####################################################################');
        Console::println('[eXpension Pack] GameMode Changed Shutting down uncompatible plugins');

        foreach ($this->exp_getGameModeCompability() as $plugin => $compability) {
            $parts = explode('\\', $plugin);
            $plugin_id = $parts[1] . '\\' . $parts[2];
            if (!$plugin::exp_checkGameCompability()) {
                try {
                    $this->callPublicMethod($plugin_id, 'exp_unload');
                } catch (\Exception $ex) {
                    
                }
            }
        }
        Console::println('#####################################################################' . "\n");
    }

    private function checkPluginsOnHold() {
        Console::println('#####################################################################');
        Console::println('[eXpension Pack] GameMode Changed Starting compatible plugins');

        if (!empty(types\BasicPlugin::$plugins_onHold)) {
            $pHandler = \ManiaLive\PluginHandler\PluginHandler::getInstance();
            foreach (types\BasicPlugin::$plugins_onHold as $plugin_id) {
                $parts = explode("\\", $plugin_id);
                $className = '\\ManiaLivePlugins\\' . $plugin_id . "\\" . $parts[1];
                if ($className::exp_checkGameCompability()) {
                    $pHandler->load($plugin_id);
                }
            }
        }
        Console::println('#####################################################################' . "\n");
    }

    public function showInfo($login) {
        $info = Gui\Windows\InfoWindow::Create($login);
        $info->setTitle("Server info");
        $info->centerOnScreen();
        $info->setSize(120, 90);
        $info->show();
    }

    public function onPostLoop() {

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
        $this->update = true;
        $this->resetExpPlayers();
    }

    public function onBeginRound() {
        $this->update = true;
        $this->resetExpPlayers();
    }

    public function onEndRound() {

        $rankings = $this->connection->getCurrentRanking(-1, 0);
        if (count($rankings) > 0) {
            foreach ($rankings as $player) {
                if (array_key_exists($player->login, $this->expPlayers)) {
                    $this->expPlayers[$player->login]->matchScore += $player->score;
                }
            }
        }
    }

    public function onPlayerInfoChanged($playerInfo) {
        $this->update = true;
        $player = \DedicatedApi\Structures\Player::fromArray($playerInfo);
        if (array_key_exists($player->login, $this->expPlayers)) {
            $this->expPlayers[$player->login]->teamId = $player->teamId;
        }

        if ($player->spectator == 1) {
            if (array_key_exists($player->login, $this->expPlayers)) {
                $this->expPlayers[$player->login]->hasRetired = true;
                $this->expPlayers[$player->login]->isPlaying = false;
            }
        } else {
            if (array_key_exists($player->login, $this->expPlayers)) {
                $this->expPlayers[$player->login]->isPlaying = true;
            }
        }
    }

    public function resetExpPlayers() {
        self::$roundFinishOrder = array();
        self::$checkpointOrder = array();
        foreach ($this->storage->players as $login => $player) {
            if ($player->spectator == 1)
                continue;
            if (!isset($this->expPlayers[$login])) {
                $this->expPlayers[$login] = Structures\ExpPlayer::fromArray($player->toArray());
            }
            $this->expPlayers[$login]->hasRetired = false;
            $this->expPlayers[$login]->checkpoints = array(0 => 0);
            $this->expPlayers[$login]->finalTime = -1;
            $this->expPlayers[$login]->position = -1;
            $this->expPlayers[$login]->time = -1;
            $this->expPlayers[$login]->curCpIndex = -1;
        }
    }

    public function onPlayerFinish($playerUid, $login, $timeOrScore) {
        // handle onPlayerfinish @ start from server.
        $this->update = true;
        if ($playerUid == 0)
            return;

        if (!array_key_exists($login, $this->expPlayers)) {
            $player = $this->storage->getPlayerObject($login);
            $this->expPlayers[$login] = Structures\ExpPlayer::fromArray($player->toArray());
        }

        if ($timeOrScore == 0) {
            $this->expPlayers[$login]->finalTime = 0;
            if ($this->storage->gameInfos->gameMode !== \DedicatedApi\Structures\GameInfos::GAMEMODE_TIMEATTACK) {
                $this->expPlayers[$login]->hasRetired = true;
                Dispatcher::dispatch(new Events\PlayerEvent(Events\PlayerEvent::ON_PLAYER_GIVEUP, $this->expPlayers[$login]));
            }
            return;
        }

        if ($timeOrScore > 0) {
            $this->expPlayers[$login]->finalTime = $timeOrScore;
            if ($this->storage->gameInfos->gameMode !== \DedicatedApi\Structures\GameInfos::GAMEMODE_TIMEATTACK) {
                self::$roundFinishOrder[] = $login;
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
            if (isset($player->checkpoints[0])) {
                $player->time = end($player->checkpoints);
                // $player->curCpIndex = key($player->checkpoints);
            }
            // is player is not playing ie. has become spectator or disconnect, remove...
            if (!$player->isPlaying) {
                unset($this->expPlayers[$login]);
                continue;
            }


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
