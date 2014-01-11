<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords;

use ManiaLive\Event\Dispatcher;
use ManiaLive\Utilities\Console;
use ManiaLivePlugins\eXpansion\Core\i18n\Message;
use \ManiaLivePlugins\eXpansion\LocalRecords\Config;
use \ManiaLivePlugins\eXpansion\LocalRecords\Events\Event;
use ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;

class LocalRecords extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    //This numbers are important do not change. THey are using binarie thinking.

    const DEBUG_NONE = 0;     //00000
    const DEBUG_RECS_SAVE = 1;       //00001
    const DEBUG_RECS_DB = 2;  //00010
    const DEBUG_RECS_FULL = 3;       //00011
    const DEBUG_RANKS = 4;    //00100
    const DEBUG_LAPS = 8;     //01000
    const DEBUG_RECPROCESSTIME = 16; //10000
    const DEBUG_ALL = 31;     //11111

    /**
     * Activating the debug mode of the plugin
     * @var type int
     */

    private $debug = self::DEBUG_ALL;

    /**
     * List of the records for the current track
     *
     * @var type Array int => Record
     */
    private $currentChallengeRecords = array();

    /**
     * The best times and other statistics of the current players on the server
     *
     * @var Record[] Array[$login] = Record
     */
    private $currentChallengePlayerRecords = array();
    private $currentChallangeSectorTimes = array();
    private $currentChallangeSectorsCps = array();

    /**
     * Number of maps that was played since the plugin started
     * @var int
     */
    private $map_count = 0;

    /**
     * The current 100 best ranks in the server
     * @var array int => login
     */
    private $ranks = array();

    /**
     * The rank of players connected to the
     * @var array login => int
     */
    private $player_ranks = array();

    /**
     * Total amount of players that has a rank
     * @var int
     */
    private $total_ranks = -1;
    private $ranks_reset = false;

    /**
     * Checking if we trued to get ranks before
     * @var bool
     */
    private $rank_needUpdated = true;

    /**
     * The last time of the players past the checkpoints
     * @var array login => array( int => int)
     */
    private $checkpoints = array();

    /**
     * @var Config
     */
    private $config;

    /**
     * All the messages need to be sent;
     * @var Message
     */
    private $msg_secure, $msg_new, $msg_improved, $msg_BeginMap, $msg_newMap, $msg_personalBest,
            $msg_noPB, $msg_showRank, $msg_noRank, $msg_secure_top1, $msg_secure_top5, $msg_new_top1,
            $msg_new_top5, $msg_improved_top1, $msg_improved_top5, $msg_admin_savedRecs;
    public static $txt_rank, $txt_nick, $txt_score, $txt_sector, $txt_cp,
            $txt_avgScore, $txt_nbFinish, $txt_wins, $txt_lastRec, $txt_ptime, $txt_nbRecords;

    function exp_onInit() {
        //Activating debug for records only
        $this->debug = self::DEBUG_NONE;

        //Listing the compatible Games
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_ROUNDS);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TIMEATTACK);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TEAM);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_CUP);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_LAPS);
        $this->config = Config::getInstance();

        $this->setPublicMethod("getCurrentChallangePlayerRecord");
        $this->setPublicMethod("getPlayersRecordsForAllMaps");
        $this->setPublicMethod("getRecords");
        $this->setPublicMethod("getRanks");
        $this->setPublicMethod("getPlayerRank");
        $this->setPublicMethod("getTotalRanked");
        $this->setPublicMethod("showRecsWindow");

        //The Database plugin is needed. 
        $this->addDependency(new \ManiaLive\PluginHandler\Dependency("eXpansion\Database"));

        //Oliverde8 Menu
        if ($this->isPluginLoaded('oliverde8\HudMenu')) {
            Dispatcher::register(\ManiaLivePlugins\oliverde8\HudMenu\onOliverde8HudMenuReady::getClass(), $this);
        }
    }

    public function exp_onLoad() {

        parent::exp_onLoad();

        //Recovering the multi language messages
        $this->msg_secure = exp_getMessage($this->config->msg_secure);
        $this->msg_new = exp_getMessage($this->config->msg_new);
        $this->msg_equals = exp_getMessage($this->config->msg_equals);
        $this->msg_improved = exp_getMessage($this->config->msg_improved);


        $this->msg_secure_top5 = exp_getMessage($this->config->msg_secure_top5);
        $this->msg_new_top5 = exp_getMessage($this->config->msg_new_top5);
        $this->msg_equals_top5 = exp_getMessage($this->config->msg_equals_top5);
        $this->msg_improved_top5 = exp_getMessage($this->config->msg_improved_top5);

        $this->msg_secure_top1 = exp_getMessage($this->config->msg_secure_top1);
        $this->msg_new_top1 = exp_getMessage($this->config->msg_new_top1);
        $this->msg_equals_top1 = exp_getMessage($this->config->msg_equals_top1);
        $this->msg_improved_top1 = exp_getMessage($this->config->msg_improved_top1);

        $this->msg_newMap = exp_getMessage($this->config->msg_newMap);
        $this->msg_BeginMap = exp_getMessage($this->config->msg_BeginMap);
        $this->msg_personalBest = exp_getMessage($this->config->msg_personalBest);
        $this->msg_noPB = exp_getMessage($this->config->msg_noPB);
        $this->msg_showRank = exp_getMessage($this->config->msg_showRank);
        $this->msg_noRank = exp_getMessage($this->config->msg_noRank);
        $this->msg_admin_savedRecs = exp_getMessage('#admin_action#Records saved sucessfully into the database');

        self::$txt_rank = exp_getMessage("#");
        self::$txt_nick = exp_getMessage("NickName");
        self::$txt_score = exp_getMessage("Score");
        self::$txt_sector = exp_getMessage("Sector");
        self::$txt_cp = exp_getMessage("CheckPoint Times");
        self::$txt_avgScore = exp_getMessage("Average Score");
        self::$txt_nbFinish = exp_getMessage("Finishes");
        self::$txt_wins = exp_getMessage("Nb Wins");
        self::$txt_lastRec = exp_getMessage("Last Rec Date");
        self::$txt_ptime = exp_getMessage("Play Time");
        self::$txt_nbRecords = exp_getMessage("nb Rec");

        $this->enableStorageEvents();
        $this->enableDedicatedEvents();
        $this->enableDatabase();

        //List of all records
        $cmd = $this->registerChatCommand("recs", "showRecsWindow", 0, true);
        $cmd->help = 'Show Records Window';

        //Top 100 ranked players
        $cmd = $this->registerChatCommand("top100", "showRanksWindow", 0, true);
        $cmd->help = 'Show Server Ranks Window';

        $cmd = $this->registerChatCommand("ranks", "showRanksWindow", 0, true);
        $cmd->help = 'Show Server Ranks Window';

        $cmd = $this->registerChatCommand("rank", "chat_showRank", 0, true);
        $cmd->help = 'Show Player Rank';

        $cmd = $this->registerChatCommand("pb", "chat_personalBest", 0, true);
        $cmd->help = 'Show Player Personal Best';

        $cmd = $this->registerChatCommand("cps", "showCpWindow", 0, true);
        $cmd->help = 'Show Checkpoint times';

        $cmd = $this->registerChatCommand("sectors", "showSectorWindow", 0, true);
        $cmd->help = 'Show Players Best Sector times';

        $cmd = AdminGroups::addAdminCommand("saverecs", $this, "chat_forceSave", "records_save");
        $cmd->setHelp("Will force the save of the records changes in the Database");
    }

    public function exp_onReady() {
        parent::exp_onReady();

        //Creating the records table
        if (!$this->db->tableExists("exp_records")) {
            $q = "CREATE TABLE `exp_records` (
                    `record_id` MEDIUMINT( 9 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                    `record_challengeuid` VARCHAR( 27 ) NOT NULL DEFAULT '0',
                    `record_playerlogin` VARCHAR( 30 ) NOT NULL DEFAULT '0',
                    `record_nbLaps` INT( 3 ) NOT NULL,
                    `record_score` MEDIUMINT( 9 ) DEFAULT '0',
                    `record_nbFinish` MEDIUMINT( 4 ) DEFAULT '0',
                    `record_avgScore` MEDIUMINT( 9 ) DEFAULT '0',
                    `record_checkpoints` TEXT,
                    `record_date` INT( 9 ) NOT NULL,
                    KEY(`record_challengeuid` ,  `record_playerlogin` ,  `record_nbLaps`)
                ) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = MYISAM ;";
            $this->db->query($q);
        }

        //Checking the version if the table
        $version = $this->callPublicMethod('eXpansion\Database', 'getDatabaseVersion', 'exp_records');
        if (!$version) {
            $version = $this->callPublicMethod('eXpansion\Database', 'setDatabaseVersion', 'exp_records', 1);
        }

        if (!$this->db->tableExists("exp_ranks")) {
            $q = "CREATE TABLE `exp_ranks` (
                    `rank_playerlogin` VARCHAR( 30 ) NOT NULL DEFAULT '0',
                    `rank_rank` INT(6) NOT NULL DEFAULT '0',
                    `rank_challengeuid` VARCHAR( 27 ) NOT NULL DEFAULT '0',
                    `rank_nbLaps` INT( 3 ) NOT NULL,
                    KEY(`rank_challengeuid` ,  `rank_playerlogin` ,  `rank_nbLaps`)
                ) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = MYISAM ;";
            $this->db->query($q);
            $this->resetRanks();
        }

        $this->onBeginMap("", "", "");
        if ($this->isPluginLoaded('eXpansion\Menu')) {
            $this->callPublicMethod('eXpansion\Menu', 'addSeparator', __('Records'), true);
            $this->callPublicMethod('eXpansion\Menu', 'addItem', __('Map Records'), null, array($this, 'showRecsMenuItem'), false);
        }
        

        $time = microtime(true);
        echo "Reseting Maps ...";
        $this->resetRanks();
        echo "Done in : ".(microtime(true) - $time)."\n\n";
        
        $this->getRanks();
        $this->updateCurrentChallengeRecords();      

    }

    public function showRecsMenuItem($login) {
        $this->showRecsWindow($login);
    }

    /**
     * getPlayersRecordsForAllMaps($login)
     * 
     * @param string $login
     * @return array $list -> $list[mapuid] = (int) position
     */
    public function getPlayersRecordsForAllMaps($login) {
        $q = ' SELECT `record_playerlogin`,`record_score`,`record_challengeuid` FROM `exp_records` order by `record_challengeuid` asc,`record_score` asc';
        $data = $this->db->query($q);
        $list = array();
        $last = "";
        $pos = 1;
        while ($row = $data->fetchObject()) {
            // check for new map & reset rank
            if ($last != $row->record_challengeuid) {
                $last = $row->record_challengeuid;
                $pos = 1;
            }
            if (isset($list[$row->record_challengeuid]))
                continue;

            // store player's maps & records
            if ($row->record_playerlogin == $login) {
                $list[$row->record_challengeuid] = $pos;
                continue;
            }
            $pos++;
        }
        return $list;
    }

    public function onBeginMap($map, $warmUp, $matchContinuation) {

        //We get all the records
        $this->updateCurrentChallengeRecords();
        //New map, so map count ++
        $this->map_count++;

        //Checking for lap constraints
        if ($this->useLapsConstraints()) {
            $nbLaps = $this->getNbOfLaps();
        } else {
            $nbLaps = 1;
        }

        if (($this->debug & self::DEBUG_LAPS) == self::DEBUG_LAPS)
            echo "[DEBUG LocalRecs]Nb Laps : " . $nbLaps . "\n";

        //Sending begin map messages
        if (sizeof($this->currentChallengeRecords) == 0 && $this->config->sendBeginMapNotices) {
            $this->exp_chatSendServerMessage($this->msg_newMap, null, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->currentMap->name, 'wosnm')));
        } else if ($this->config->sendBeginMapNotices) {
            $time = \ManiaLive\Utilities\Time::fromTM($this->currentChallengeRecords[0]->time);
            if (substr($time, 0, 2) === "0:") {
                $time = substr($time, 2);
            }
            $this->exp_chatSendServerMessage($this->msg_BeginMap, null, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->currentMap->name, 'wosnm'), $time, \ManiaLib\Utils\Formatting::stripCodes($this->currentChallengeRecords[0]->nickName, 'wosnm')));
            foreach ($this->storage->players as $login => $player) {
                $this->chat_personalBest($login, null);
            }
            foreach ($this->storage->spectators as $login => $player) {
                $this->chat_personalBest($login, null);
            }
        }
        //send Ranking
        if ($this->config->sendRankingNotices) {
            foreach ($this->storage->players as $login => $player)
                $this->chat_showRank($login);

            foreach ($this->storage->spectators as $login => $player)
                $this->chat_showRank($login);
        }
    }

    public function onEndMatch($rankings, $winnerTeamOrMap) {

        //Checking for lap constraints
        if ($this->useLapsConstraints()) {
            $nbLaps = $this->getNbOfLaps();
        } else {
            $nbLaps = 1;
        }

        if (($this->debug & self::DEBUG_LAPS) == self::DEBUG_LAPS)
            echo "[DEBUG LocalRecs]Nb Laps : " . $nbLaps . "\n";

        $updated = false;

        //We update the database
        //Firs of the best records
        foreach ($this->currentChallengeRecords as $i => $record) {
            $updated = $updated || $this->updateRecordInDatabase($record, $nbLaps);
        }
        //Now the rest of the times as well(PB)
        foreach ($this->currentChallengePlayerRecords as $i => $record) {
            $updated = $updated || $this->updateRecordInDatabase($record, $nbLaps);
        }

        if ($updated) {
            $this->updateRanks($this->storage->currentMap->uId, $nbLaps);
        }
    }

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {

        if ($this->ranks_reset)
            $this->resetRanks();

        // Added this to calulate the new ranks during every map change -reaby
        $this->rank_needUpdated = true;
        $this->getRanks();
    }

    public function onPlayerConnect($login, $isSpectator) {
        $uid = $this->storage->currentMap->uId;
        //If the player doesn't have a record get best time and other...
        $this->getFromDbPlayerRecord($login, $uid);

        //Send a message telling him about records on this map
        if (sizeof($this->currentChallengeRecords) == 0 && $this->config->sendBeginMapNotices) {
            $this->exp_chatSendServerMessage($this->msg_newMap, $login, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->currentMap->name, 'wosnm')));
        } else if ($this->config->sendBeginMapNotices) {
            $time = \ManiaLive\Utilities\Time::fromTM($this->currentChallengeRecords[0]->time);
            if (substr($time, 0, 2) === "0:") {
                $time = substr($time, 2);
            }
            $this->exp_chatSendServerMessage($this->msg_BeginMap, $login, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->currentMap->name, 'wosnm'), $time, \ManiaLib\Utils\Formatting::stripCodes($this->currentChallengeRecords[0]->nickName, 'wosnm')));
        }

        //Get rank of the player
        if ($this->config->sendRankingNotices) {
            $this->chat_showRank($login);
        }
    }

    public function onPlayerDisconnect($login, $reason = null) {
        //Remove all checkpoints data
        $this->checkpoints[$login] = array();
        unset($this->checkpoints[$login]);
        //And rank data
        unset($this->player_ranks[$login]);
    }

    /**
     * onPlayerCheckpoint()
     * Function called when someone passes a checkpoint.
     *
     * @param mixed $playerUID
     * @param mixed $playerLogin
     * @param mixed $timeScore
     * @param mixed $currentLap
     * @param mixed $checkpointIndex
     * @return void
     */
    public function onPlayerCheckpoint($playerUid, $login, $score, $curLap, $checkpointIndex) {
        $this->checkpoints[$login][] = $score;
    }

    /**
     * onPlayerFinish()
     * Function called when a player finishes.
     *
     * @param int $playerUid
     * @param string $login
     * @param int $timeOrScore
     * @return
     */
    public function onPlayerFinish($playerUid, $login, $timeOrScore) {

        //Checking for valid time
        if (isset($this->storage->players[$login]) && $timeOrScore > 0) {
            $gamemode = $this->storage->gameInfos->gameMode;

            //If laps mode we need to ignore. Laps has it's own end map event(end finish lap)
            if ($gamemode == \DedicatedApi\Structures\GameInfos::GAMEMODE_LAPS && $this->config->lapsModeCount1lap)//Laps mode has it own on Player finish event
                return;

            $time = microtime();
            //We add the record to the buffer
            $this->addRecord($login, $timeOrScore, $gamemode, $this->checkpoints[$login]);

            if (($this->debug & self::DEBUG_RECPROCESSTIME) == self::DEBUG_RECPROCESSTIME)
                $this->console("[eXp][DEBUG][LocalRecords:RECS]#### NEW RANK IN : " . (microtime() - $time) . "s BAD?");
        }
        //We reset the checkPoints
        $this->checkpoints[$login] = array();
    }

    public function onPlayerFinishLap($player, $time, $checkpoints, $nbLap) {
        if ($this->config->lapsModeCount1lap && isset($this->storage->players[$player->login]) && $time > 0) {
            $gamemode = $this->storage->gameInfos->gameMode;

            if ($gamemode != \DedicatedApi\Structures\GameInfos::GAMEMODE_LAPS) //Laps mode has it own on Player finish event
                return;

            $this->addRecord($player->login, $time, $gamemode, $this->checkpoints[$player->login]);
            $this->checkpoints[$player->login] = array();
        }
    }

    /**
     * Called when the Oliverde8HudMenu is loaded
     * @param \ManiaLivePlugins\oliverde8\HudMenu\HudMenu
     */
    public function onOliverde8HudMenuReady($menu) {

        $parent = $menu->findButton(array("menu", "Records"));
        if (!$parent) {
            $button["style"] = "Icons128x128_1";
            $button["substyle"] = "Replay";
            $parent = $menu->addButton("menu", "Records", $button);
        }

        $button["style"] = "BgRaceScore2";
        $button["substyle"] = "ScoreLink";
        $button["plugin"] = $this;
        $button["function"] = "showRecsWindow";
        $menu->addButton($parent, "Local Records", $button);

        $button["style"] = "BgRaceScore2";
        $button["substyle"] = "LadderRank";
        $button["plugin"] = $this;
        $button["function"] = "showRanksWindow";
        $menu->addButton($parent, "Local Ranks", $button);

        $button["style"] = "Icons128x128_1";
        $button["substyle"] = "Race";
        $button["plugin"] = $this;
        $button["function"] = "showCpWindow";
        $menu->addButton($parent, "Best CheckPoints", $button);

        $button["style"] = "Icons128x128_1";
        $button["substyle"] = "Platform";
        $button["plugin"] = $this;
        $button["function"] = "showSectorWindow";
        $menu->addButton($parent, "Best Sector Times", $button);

        $parent = $menu->findButton(array("admin", "Server Options"));
        if (!$parent) {
            $button["style"] = "Icons128x128_1";
            $button["substyle"] = "Options";
            $button["plugin"] = $this;
            $parent = $menu->addButton("admin", "Server Options", $button);
            unset($button["style"]);
        }

        $parent2 = $menu->findButton(array("admin", "Server Options", "Other Options"));
        if (!$parent2) {
            $button["style"] = "Icons128x128_1";
            $button["substyle"] = "Options";
            $button["plugin"] = $this;
            $parent2 = $menu->addButton($parent, "Other Options", $button);
            unset($button["style"]);
        }

        $button["style"] = "Icons64x64_1";
        $button["substyle"] = "Save";
        $button["plugin"] = $this;
        $button["function"] = "chat_forceSave";
        $button["permission"] = "records_save";
        $menu->addButton($parent2, "Save Records", $button);
    }

    public function onMapListModified($curMapIndex, $nextMapIndex, $isListModified) {
        $this->ranks_reset = true;
    }

    /**
     * Will add a a record to the current map records buffer.
     * The record will only be save on endMap
     *
     * @param $login the login of the player who did the time
     * @param $score His score/time
     * @param $gamemode The gamemode while he did the record
     * @param $cpScore list of CheckPoint times
     */
    public function addRecord($login, $score, $gamemode, $cpScore) {
        $uid = $this->storage->currentMap->uId;
        $player = $this->storage->getPlayerObject($login);
        $force = false;
        $this->currentChallangeSectorTimes = array();

        //Player doesen't have record need to create one
        if (!isset($this->currentChallengePlayerRecords[$login])) {
            $record = new Record();
            $record->login = $login;
            $record->nickName = $player->nickName;
            $record->time = $score;
            $record->nbFinish = 1;
            $record->avgScore = $score;
            $record->gamemode = $gamemode;
            $record->nation = $player->path;
            $record->uId = $uid;
            $record->place = sizeof($this->currentChallengeRecords) + 1;
            $record->ScoreCheckpoints = $cpScore;
            $i = sizeof($this->currentChallengeRecords);
            if ($i > $this->config->recordsCount)
                $i = $this->config->recordsCount;
            $this->currentChallengeRecords[$i] = $record;
            $this->currentChallengePlayerRecords[$login] = $record;
            $this->currentChallengePlayerRecords[$login]->isNew = true;
            $force = true;
            if (($this->debug & self::DEBUG_RECS_SAVE) == self::DEBUG_RECS_SAVE)
                $this->console("[eXp][DEBUG][LocalRecords:RECS]$login just did his firs time of $score on this map");
        } else {
            //We update the old records average time and nbFinish
            $this->currentChallengePlayerRecords[$login]->nbFinish++;
            $avgScore = (($this->currentChallengePlayerRecords[$login]->nbFinish - 1) * $this->currentChallengePlayerRecords[$login]->avgScore + $score ) / $this->currentChallengePlayerRecords[$login]->nbFinish;
            $this->currentChallengePlayerRecords[$login]->avgScore = $avgScore;

            if (($this->debug & self::DEBUG_RECS_SAVE) == self::DEBUG_RECS_SAVE)
                $this->console("[eXp][DEBUG][LocalRecords:RECS]$login just did a new time of $score. His current rank is " . $this->currentChallengePlayerRecords[$login]->place);
        }

        $nrecord = $this->currentChallengePlayerRecords[$login];

        //We flag it as it needs to be updated in the database as well
        $nrecord->isUpdated = true;

        //Now we need to find it's rank
        if ($force || $nrecord->time > $score) {

            //Saving old rank and time
            $recordrank_old = $nrecord->place;
            $recordtime_old = $nrecord->time;

            //Updating time with new time/score
            $nrecord->time = $score;

            //Update the checkoints
            $nrecord->ScoreCheckpoints = $cpScore;
            //And the date on which the record was driven
            $nrecord->date = time();

            //Now we need to try and find a rank to the time
            $i = $recordrank_old - 2;

            //IF old rank was to bad to take in considaration. Let's try the worst record and see
            if ($i >= $this->config->recordsCount)
                $i = $this->config->recordsCount - 1;

            if (($this->debug & self::DEBUG_RECS_FULL) == self::DEBUG_RECS_FULL)
                $this->console("[eXp][DEBUG][LocalRecords:RECS]Starting to look for the rank of $login 's record at rank $i+1");

            $firstRecord = ($i < 0);

            //For each record worse then the new, push it back and push forward the new one
            while ($i >= 0 && $this->currentChallengeRecords[$i]->time > $nrecord->time) {
                $record = $this->currentChallengeRecords[$i];

                if (($this->debug & self::DEBUG_RECS_FULL) == self::DEBUG_RECS_FULL)
                    $this->console("[eXp][DEBUG][LocalRecords:RECS]$login is getting better : " . $nrecord->place . "=>" . ($nrecord->place - 1)
                            . "And " . $record->login . " is getting worse" . $record->place . "=>" . ($record->place + 1));

                //New record takes old recs place
                $this->currentChallengeRecords[$i] = $nrecord;
                //and old takes new recs place
                $this->currentChallengeRecords[$i + 1] = $record;
                //Old record get's worse
                $record->place = $i + 2;
                //new get's better
                $nrecord->place = $i + 1;
                $i--;
            }

            if ($firstRecord) {
                $nrecord->place = 1;
            }

            if (($this->debug & self::DEBUG_RECS_SAVE) == self::DEBUG_RECS_SAVE)
                $this->console("[eXp][DEBUG][LocalRecords:RECS]$login new rec Rank found" . $nrecord->place . " Old was : " . $recordrank_old);

            /*
             * Found new Rank sending message
             */
            //Formating Time
            $time = \ManiaLive\Utilities\Time::fromTM($nrecord->time);
            if (substr($time, 0, 2) === "0:") {
                $time = substr($time, 2);
            }

            //No new rank, just drove a better time
            if ($nrecord->place == $recordrank_old && !$force && $nrecord->place <= $this->config->recordsCount) {
                $securedBy = \ManiaLive\Utilities\Time::fromTM($nrecord->time - $recordtime_old);
                if (substr($securedBy, 0, 3) === "0:0") {
                    $securedBy = substr($securedBy, 3);
                } else if (substr($securedBy, 0, 2) === "0:") {
                    $securedBy = substr($securedBy, 2);
                }
                // equals time
                if ($nrecord->time == $recordtime_old) {
                    $msg = $this->msg_equals;
                    if ($nrecord->place <= 5) {
                        $msg = $this->msg_equals_top5;
                        if ($nrecord->place == 1)
                            $msg = $this->msg_equas_top1;
                    }
                    if ($nrecord->place <= $this->config->recordPublicMsgTreshold) {
                        $this->exp_chatSendServerMessage($msg, null, array(\ManiaLib\Utils\Formatting::stripCodes($nrecord->nickName, 'wosnm'), $nrecord->place, $time));
                    } else {
                        $this->exp_chatSendServerMessage($msg, $login, array(\ManiaLib\Utils\Formatting::stripCodes($nrecord->nickName, 'wosnm'), $nrecord->place, $time));
                    }
                    // improves time
                } else {
                    $msg = $this->msg_secure;
                    if ($nrecord->place <= 5) {
                        $msg = $this->msg_secure_top5;
                        if ($nrecord->place == 1)
                            $msg = $this->msg_secure_top1;
                    }
                    if ($nrecord->place <= $this->config->recordPublicMsgTreshold) {
                        $this->exp_chatSendServerMessage($msg, null, array(\ManiaLib\Utils\Formatting::stripCodes($nrecord->nickName, 'wosnm'), $nrecord->place, $time, $recordrank_old, $securedBy));
                    } else {
                        $this->exp_chatSendServerMessage($msg, $login, array(\ManiaLib\Utils\Formatting::stripCodes($nrecord->nickName, 'wosnm'), $nrecord->place, $time, $recordrank_old, $securedBy));
                    }
                }

                \ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_UPDATE_RECORDS, $this->currentChallengeRecords));
            }
            //Improved time and new Rank
            else if ($nrecord->place < $recordrank_old && !$force && $nrecord->place <= $this->config->recordsCount) {
                $securedBy = \ManiaLive\Utilities\Time::fromTM($nrecord->time - $recordtime_old);
                if (substr($securedBy, 0, 3) === "0:0") {
                    $securedBy = substr($securedBy, 3);
                } else if (substr($securedBy, 0, 2) === "0:") {
                    $securedBy = substr($securedBy, 2);
                }

                $msg = $this->msg_improved;
                if ($nrecord->place <= 5) {
                    $msg = $this->msg_improved_top5;
                    if ($nrecord->place == 1)
                        $msg = $this->msg_improved_top1;
                }

                if ($nrecord->place <= $this->config->recordPublicMsgTreshold) {
                    $this->exp_chatSendServerMessage($msg, null, array(\ManiaLib\Utils\Formatting::stripCodes($nrecord->nickName, 'wosnm'), $nrecord->place, $time, $recordrank_old, $securedBy));
                } else {
                    $this->exp_chatSendServerMessage($msg, $login, array(\ManiaLib\Utils\Formatting::stripCodes($nrecord->nickName, 'wosnm'), $nrecord->place, $time, $recordrank_old, $securedBy));
                }

                \ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_UPDATE_RECORDS, $this->currentChallengeRecords));
            }
            //First record the player drove
            else if ($nrecord->place <= $this->config->recordsCount) {
                $msg = $this->msg_new;
                if ($nrecord->place <= 5) {
                    $msg = $this->msg_new_top5;
                    if ($nrecord->place == 1)
                        $msg = $this->msg_new_top1;
                }
                if ($nrecord->place <= $this->config->recordPublicMsgTreshold) {
                    $this->exp_chatSendServerMessage($msg, null, array(\ManiaLib\Utils\Formatting::stripCodes($nrecord->nickName, 'wosnm'), $nrecord->place, $time));
                } else {
                    $this->exp_chatSendServerMessage($msg, $login, array(\ManiaLib\Utils\Formatting::stripCodes($nrecord->nickName, 'wosnm'), $nrecord->place, $time));
                }

                \ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_UPDATE_RECORDS, $this->currentChallengeRecords));
            }
            \ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_PERSONAL_BEST, $nrecord));
        }
    }

    /**
     * Will update the record in the database.
     * @param Record $record
     * @param $nbLaps
     */
    private function updateRecordInDatabase(Record $record, $nbLaps) {
        //$uid = $this->storage->currentMap->uId;
        $uid = $record->uId;
        if ($record->isNew) {
            //If the record is new we insert
            $q = 'INSERT INTO `exp_records` (`record_challengeuid`, `record_playerlogin`, `record_nbLaps`
                            ,`record_score`, `record_nbFinish`, `record_avgScore`, `record_checkpoints`, `record_date`)
                        VALUES(' . $this->db->quote($uid) . ',
                            ' . $this->db->quote($record->login) . ',
                            ' . $this->db->quote($nbLaps) . ',
                            ' . $this->db->quote($record->time) . ',
                            ' . $this->db->quote($record->nbFinish) . ',
                            ' . $this->db->quote($record->avgScore) . ',
                            ' . $this->db->quote(implode(",", $record->ScoreCheckpoints)) . ',
                            ' . $this->db->quote($record->date) . '
                        )';
            $this->db->query($q);
            $record->isNew = false;
        } else if ($record->isUpdated) {
            //If it isn't but it has been updated we update
            $q = 'UPDATE `exp_records`
                        SET `record_score` = ' . $this->db->quote($record->time) . ',
                            `record_nbFinish` = ' . $this->db->quote($record->nbFinish) . ',
                            `record_avgScore` = ' . $this->db->quote($record->avgScore) . ',
                            `record_checkpoints` = ' . $this->db->quote(implode(",", $record->ScoreCheckpoints)) . ',
                            `record_date` = ' . $this->db->quote($record->date) . '
                        WHERE `record_challengeuid` = ' . $this->db->quote($uid) . '
                            AND `record_playerlogin` =  ' . $this->db->quote($record->login) . '
                            AND `record_nbLaps` = ' . $this->db->quote($nbLaps) . ';';

            $this->db->query($q);
        }
        //We flag it as updated
        $record->isUpdated = false;
    }

    /**
     * updateCurrentChallengeRecords()
     * Updates currentChallengePlayerRecords and the currentChallengeRecords arrays
     * with the current Challange Records.
     *
     * @return void
     */
    private function updateCurrentChallengeRecords() {

        $this->currentChallangeSectorTimes = array();
        $this->currentChallangeSectorsCps = $this->calcCP($this->storage->currentMap->nbCheckpoints);

        $this->currentChallengePlayerRecords = array(); //reset
        $this->currentChallengeRecords = array(); //reset
        //Fetch best records
        $this->currentChallengeRecords = $this->buildCurrentChallangeRecords(); // fetch

        $uid = $this->storage->currentMap->uId;

        //Getting current players records
        foreach ($this->storage->players as $login => $player) { // get players
            $this->getFromDbPlayerRecord($login, $uid);
        }

        //Getting current spectators records
        foreach ($this->storage->spectators as $login => $player) { // get spectators
            $this->getFromDbPlayerRecord($login, $uid);
        }

        //Dispatch event
        \ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_UPDATE_RECORDS, $this->currentChallengeRecords));
        //\ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_NEW_RECORD, $this->currentChallengeRecords));
    }

    /**
     * buildCurrentChallangeRecords().
     * It will get the list of records of this map from the database
     *
     * @param mixed $gamemode
     * @return
     */
    private function buildCurrentChallangeRecords($gamemode = NULL) {
        $challenge = $this->storage->currentMap;

        if ($gamemode === NULL || $gamemode == '') {
            $gamemode = $this->storage->gameInfos->gameMode;
        }

        $cons = "";
        if ($this->useLapsConstraints()) {
            $cons .= " AND record_nbLaps = " . $this->getNbOfLaps();
        } else {
            $cons .= " AND record_nbLaps = 1";
        }

        $q = "SELECT * FROM `exp_records`, `exp_players`
                    WHERE `record_challengeuid` = " . $this->db->quote($challenge->uId) . " " . $cons . "
                        AND `exp_records`.`record_playerlogin` = `exp_players`.`player_login`
                        " . $cons . "
                    ORDER BY `record_score` ASC
                    LIMIT 0, " . $this->config->recordsCount . ";";

        $dbData = $this->db->query($q);

        if ($dbData->recordCount() == 0) {
            return array();
        }

        $i = 1;
        $records = array();

        while ($data = $dbData->fetchStdObject()) {

            $record = new Record();
            $this->currentChallengePlayerRecords[$data->record_playerlogin] = $record;

            $record->place = $i;
            $record->login = $data->record_playerlogin;
            $record->nickName = $data->player_nickname;
            $record->time = $data->record_score;
            $record->nbFinish = $data->record_nbFinish;
            $record->avgScore = $data->record_avgScore;
            $record->nation = $data->player_nation;
            $record->ScoreCheckpoints = explode(",", $data->record_checkpoints);
            $record->uId = $this->storage->currentMap->uId;

            $records[$i - 1] = $record;
            $i++;
        }

        return $records;
    }

    /**
     * deleteRecord()
     * deletes a record from database for map.
     * @param \DedicatedApi\Structures\Map $challenge
     * @param string $login
     * @return boolean
     */
    private function deleteRecord(\DedicatedApi\Structures\Map $challenge, $login) {

        $q = "DELETE FROM `exp_records` WHERE `exp_records`.`record_challengeuid` = " . $this->db->quote($challenge->uId) . " and " .
                "`exp_records`.`record_playerlogin` = " . $this->db->quote($recordLogin) . ";";
        try {
            $this->db->query($q);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * getRecordsForMap().
     * gets the records for a map and returns array of record objects
     *
     * @param mixed $gamemode
     * @param Map $challenge
     * @param string $plugin
     * @return array(Record)
     */
    public function getRecordsForMap($gamemode = NULL, $challenge = NULL, $plugin = Null) {

        if ($challenge === NULL || $challenge == '') {
            $challenge = $this->storage->currentMap;
        }

        if ($gamemode === NULL || $gamemode == '') {
            $gamemode = $this->storage->gameInfos->gameMode;
        }

        $cons = "";
        if ($this->useLapsConstraints()) {
            $cons .= " AND record_nbLaps = " . $this->getNbOfLaps();
        } else {
            $cons .= " AND record_nbLaps = 1";
        }

        $q = "SELECT * FROM `exp_records`, `exp_players`
                    WHERE `record_challengeuid` = " . $this->db->quote($challenge->uId) . " " . $cons . "
                        AND `exp_records`.`record_playerlogin` = `exp_players`.`player_login`
                        " . $cons . "
                    ORDER BY `record_score` ASC
                    LIMIT 0, " . $this->config->recordsCount . ";";

        $dbData = $this->db->query($q);

        if ($dbData->recordCount() == 0) {
            return array();
        }

        $i = 1;
        $records = array();

        while ($data = $dbData->fetchStdObject()) {

            $record = new Record();
            // $this->currentChallengePlayerRecords[$data->record_playerlogin] = $record;

            $record->place = $i;
            $record->login = $data->record_playerlogin;
            $record->nickName = $data->player_nickname;
            $record->time = $data->record_score;
            $record->nbFinish = $data->record_nbFinish;
            $record->avgScore = $data->record_avgScore;
            $record->nation = $data->player_nation;
            $record->ScoreCheckpoints = explode(",", $data->record_checkpoints);
            $record->uId = $this->storage->currentMap->uId;

            $records[$i - 1] = $record;
            $i++;
        }

        return $records;
    }

    /**
     * getPlayerRecord()
     * Helper function, gets the record of the asked player.
     *
     * @param mixed $login
     * @param mixed $uId
     * @return Record $record
     */
    private function getFromDbPlayerRecord($login, $uId) {

        if (isset($this->currentChallengePlayerRecords[$login]))
            return $this->currentChallengePlayerRecords[$login];

        $cons = "";
        if ($this->useLapsConstraints()) {
            $cons .= " AND record_nbLaps = " . $this->getNbOfLaps();
        } else {
            $cons .= " AND record_nbLaps = 1";
        }

        $q = "SELECT * FROM `exp_records`, `exp_players`
                WHERE `record_challengeuid` = " . $this->db->quote($uId) . "
                    AND `record_playerlogin` = " . $this->db->quote($login) . "
                    AND `player_login` = `record_playerlogin`
                    " . $cons . ";";

        $dbData = $this->db->query($q);
        if ($dbData->recordCount() > 0) {

            $record = new Record();
            $data = $dbData->fetchStdObject();

            $record->place = $this->config->recordsCount + 1;
            $record->login = $data->record_playerlogin;
            $record->nickName = $data->player_nickname;
            $record->time = $data->record_score;
            $record->nbFinish = $data->record_nbFinish;
            $record->avgScore = $data->record_avgScore;
            $record->date = $data->record_date;
            $record->nation = $data->player_nation;
            $record->ScoreCheckpoints = explode(",", $data->record_checkpoints);
            $record->uId = $this->storage->currentMap->uId;

            $this->currentChallengePlayerRecords[$login] = $record;
        } else {
            return false;
        }
    }

    public function getCurrentChallangePlayerRecord($login) {
        return isset($this->currentChallengePlayerRecords[$login]) ? $this->currentChallengePlayerRecords[$login] : null;
    }

    public function getRecords() {
        return $this->currentChallengeRecords;
    }

    /**
     * useLapsConstraints()
     * Helper function, checks game mode.
     *
     * @return int $laps
     */
    public function useLapsConstraints() {
        if (!$this->config->lapsModeCount1lap) {
            $gamemode = $this->storage->gameInfos->gameMode;

            if ($gamemode == \DedicatedApi\Structures\GameInfos::GAMEMODE_TIMEATTACK || $gamemode == \DedicatedApi\Structures\GameInfos::GAMEMODE_LAPS || $gamemode == \DedicatedApi\Structures\GameInfos::GAMEMODE_ROUNDS || $gamemode == \DedicatedApi\Structures\GameInfos::GAMEMODE_CUP) {
                $nbLaps = $this->getNbOfLaps();
                if ($nbLaps > 1) {
                    return $this->storage->currentMap->lapRace;
                }
            }
        }
        return false;
    }

    /**
     * getNbOfLaps()
     * Helper function, gets number of laps.
     *
     * @return int $laps
     */
    public function getNbOfLaps() {
        switch ($this->storage->gameInfos->gameMode) {
            case \DedicatedApi\Structures\GameInfos::GAMEMODE_ROUNDS:
                if ($this->storage->gameInfos->roundsForcedLaps == 0)
                    return $this->storage->currentMap->nbLaps;
                else
                    return $this->storage->gameInfos->roundsForcedLaps;

            case \DedicatedApi\Structures\GameInfos::GAMEMODE_TEAM:
            case \DedicatedApi\Structures\GameInfos::GAMEMODE_CUP:
                return $this->storage->currentMap->nbLaps;
                break;

            case \DedicatedApi\Structures\GameInfos::GAMEMODE_LAPS:
                return $this->storage->gameInfos->lapsNbLaps;
                break;

            default:
                return 1;
        }
    }

    /**
     * showRecsWindow()
     * 
     * Display a window for a login with best times
     * @param type $login
     * @param \DedicatedApi\Structures\Map $map (optional)
     */
    public function showRecsWindow($login, $map = NULL) {
        Gui\Windows\Records::Erase($login);
        try {
            if ($map === NULL) {
                $records = $this->currentChallengeRecords;
                $map = $this->storage->currentMap;
            } else {
                $records = $this->getRecordsForMap(null, $map);
            }

            $window = Gui\Windows\Records::Create($login);
            $window->setTitle(__('Records on a Map', $login));
            $window->centerOnScreen();
            $window->populateList($records, $this->config->recordsCount);
            $window->setSize(120, 100);
            $window->show();
        } catch (\Exception $e) {
            $this->exp_chatSendServerMessage("Error: %s", $login, array($e->getMessage()));
        }
    }

    /**
     * Will show a window with the 100 best ranked players
     * @param $login
     */
    public function showRanksWindow($login) {
        Gui\Windows\Ranks::Erase($login);

        $window = Gui\Windows\Ranks::Create($login);
        $window->setTitle(__('Server Ranks', $login));
        $window->centerOnScreen();
        $window->populateList($this->getRanks(), 100);
        $window->setSize(140, 100);
        $window->show();
    }

    public function showCpWindow($login) {
        Gui\Windows\Ranks::Erase($login);

        $window = Gui\Windows\Cps::Create($login);
        $window->setTitle(__('CheckPoints on Map', $login));
        $window->populateList($this->currentChallengeRecords, 100);
        $window->setSize(200, 100);
        $window->centerOnScreen();
        $window->show();
    }

    public function showSectorWindow($login) {

        if (empty($this->currentChallangeSectorTimes)) {

            $secs = array();

            foreach ($this->currentChallengePlayerRecords as $rec)
                for ($cpt = 0; $cpt < sizeof($this->currentChallangeSectorsCps); $cpt++) {
                    $currentIndex = $this->currentChallangeSectorsCps[$cpt] - 1;
                    $prevIndex = $cpt == 0 ? -1 : $this->currentChallangeSectorsCps[$cpt - 1] - 1;

                    if (isset($rec->ScoreCheckpoints[$currentIndex])) {
                        $old = ($prevIndex < 0) ? 0 : (isset($rec->ScoreCheckpoints[$prevIndex]) ? $rec->ScoreCheckpoints[$prevIndex] : 0);
                        $secs[$cpt][] = array('sectorTime' => $rec->ScoreCheckpoints[$currentIndex] - $old,
                            'recordObj' => $rec);
                    }
                }

            $i = 0;
            foreach ($secs as $sec) {
                $this->currentChallangeSectorTimes[$i] = $this->array_sort($sec, 'sectorTime');
                $i++;
            }
        }

        $window = Gui\Windows\Sector::Create($login);
        $window->setTitle(__('Sector Times on Map', $login));
        $window->populateList($this->currentChallangeSectorTimes, 100);
        $window->setSize(160, 100);
        $window->centerOnScreen();
        $window->show();
    }

    private function calcCP($totalcps) {
        $cpsect = floor($totalcps * 0.33);
        $sect = 0;
        $cp = 0;
        $array = array();

        for ($x = 0; $x < $totalcps; $x++) {
            if ($x % ($cpsect + 1) == 0) {
                $cp++;
                $sect++;
                $array[$sect - 1] = $cp;
            } else {
                $cp++;
                $array[$sect - 1] = $cp;
            }
        }
        return $array;
    }

    private function array_sort($array, $on, $order = SORT_ASC) {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }
            print_r($sortable_array);
            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }
        return $new_array;
    }

    /**
     * Ranks of all players online on the server
     * @return array
     */
    public function getOnlineRanks() {
        return $this->player_ranks;
    }

    /**
     * 
     */
    private function resetRanks() {
        $this->db->query('DELETE FROM exp_ranks WHERE rank_challengeuid IN (' . $this->getUidSqlString() . ')');
        $this->ranks = array();
        $this->player_ranks = array();

        foreach ($this->storage->maps as $map) {
            $this->updateRanks($map->uId, 1);
        }
    }

    private function updateRanks($challengeId, $nbLaps) {
        $this->db->query('DELETE FROM exp_ranks 
                            WHERE rank_challengeuid = \'' . $challengeId . '\'
                                AND rank_nbLaps = ' . $nbLaps);

        $q = 'INSERT INTO exp_ranks 
                SELECT record_playerlogin, 
                        (SELECT Count(*) FROM exp_records r2
                            WHERE r1.record_challengeuid = r2.record_challengeuid
                                AND r1.record_nbLaps = r2.record_nbLaps
                                AND r2.record_score < r1.record_score
                                ORDER BY record_score ASC) as rank,
                        record_challengeuid, record_nbLaps
                FROM exp_records r1
                WHERE record_challengeuid = \'' . $challengeId . '\'
                                AND record_nbLaps = ' . $nbLaps . '
                GROUP BY record_playerlogin, record_challengeuid, record_nbLaps
                ORDER BY record_score ASC
                LIMIT 0, 100';
        $this->db->query($q);
    }

    /**
     * The Total number of player ranked
     * @return int
     */
    public function getTotalRanked() {
        if ($this->total_ranks == -1 || ($this->map_count % $this->config->nbMap_rankProcess) == 0) {
            $q = 'SELECT Count(DISTINCT rank_playerlogin) as nbRanked
                    FROM exp_ranks
                    WHERE rank_challengeuid IN (' . $this->getUidSqlString() . ');';

            $data = $this->db->query($q);

            if ($data->recordCount() == 0) {
                $this->total_ranks = -1;
            } else {
                $vals = $data->fetchStdObject();
                $this->total_ranks = $vals->nbRanked;
            }
        }

        return $this->total_ranks;
    }

    /**
     * Returns the players server rank as it is buffered.
     *
     * @param $login
     * @return int
     */
    public function getPlayerRank($login) {
        $id = -1;
        foreach ($this->ranks as $id => $class) {
            if (!property_exists($class, "rank_playerlogin"))
                return -1;

            if ($class->rank_playerlogin == $login)
                return $id + 1;
        }
        return -1; // added failsafe
    }

    /**
     * Returns the players server rank as it is buffered.
     *
     * @param $login
     * @return int
     */
    public function getPlayerRank_old($login) {

        if (!isset($this->player_ranks[$login]) || ($this->map_count % $this->config->nbMap_rankProcess) == 0) {

            $nbTrack = sizeof($this->storage->maps);
            $uids = $this->getUidSqlString();


            $q = 'SELECT ((SUM( rank_rank ) + (' . $nbTrack . ' - COUNT( * ) ) *' . $this->config->recordsCount . ')/' . $nbTrack . ') AS points
                    FROM exp_ranks
                    WHERE rank_playerlogin =  \'' . $login . '\'';

            echo $q . "\n\n";

            $data = $this->db->query($q);

            if ($data->recordCount() == 0) {
                $this->player_ranks[$login] = -1;
                return -1;
            } else {
                $vals = $data->fetchStdObject();
                $points = $vals->points;
                if (empty($points)) {
                    $this->player_ranks[$login] = -1;
                    return -1;
                }
            }

            $q = 'SELECT count(*) as rank
                    FROM exp_ranks
                    GROUP BY rank_playerlogin
                    HAVING ((SUM(rank_rank) + (' . $nbTrack . ' - Count(*))*' . $this->config->recordsCount . ')/' . $nbTrack . ') < ' . $points . '';

            echo $q;

            $data = $this->db->query($q);

            if ($data->recordCount() == 0) {
                $this->player_ranks[$login] = 1;
            } else {
                $vals = $data->fetchStdObject();
                $this->player_ranks[$login] = $vals->rank;
            }
        }
        return $this->player_ranks[$login];
    }

    /**
     *  Updates the bufffer of the 100 best ranked players if needed
     * @return array
     */
    public function getRanks() {
        // if ((empty($this->ranks) && $this->rank_updated) || (($this->map_count % $this->config->nbMap_rankProcess) == 0 && $this->rank_updated)) {
        if ((empty($this->ranks) && $this->rank_needUpdated)) {
            $this->rank_needUpdated = false;

            $nbTrack = sizeof($this->storage->maps);
            $uids = $this->getUidSqlString();

            $q = 'SELECT rank_playerlogin, 
                            ((SUM(rank_rank) + (' . $nbTrack . ' - Count(*))*' . $this->config->recordsCount . ')/' . $nbTrack . ') as tscore,
                            SUM(record_nbFinish) as nbFinish,
                            Count(*) as nbRecords,
                            player_nickname,
                            player_updated,
                            player_wins,
                            player_timeplayed,
                            player_nation,
                            MAX(record_date) as lastRec,
                            ' . $nbTrack . ' as nbMaps
               FROM exp_ranks rr, exp_records r, exp_players p
               WHERE rank_challengeuid IN (' . $uids . ')
                        AND rr.rank_playerlogin = r.record_playerlogin
                        AND r.record_playerlogin = p.player_login
                        AND rank_challengeuid = r.record_challengeuid
               GROUP BY rank_playerlogin,
                            player_nickname,
                            player_updated,
                            player_wins,
                            player_timeplayed,
                            player_nation
                ORDER BY tscore ASC
                LIMIT 0, 100';


            $dbData = $this->db->query($q);

            $this->ranks = array();

            if ($dbData->recordCount() == 0)
                return $this->ranks;

            while ($data = $dbData->fetchStdObject()) {
                $this->ranks[] = $data;
            }
        }
        return $this->ranks;
    }

    /**
     * Chat message displaying rank of player
     */
    public function chat_showRank($login = null) {
        if ($login != null) {
            //$rank = $this->getPlayerRank($login);
            //if ($rank == -2) {
            $rank = $this->getPlayerRank($login);
            //}
            $rankTotal = $this->getTotalRanked();
            if ($rank > 0) {
                $this->exp_chatSendServerMessage($this->msg_showRank, $login, array($rank, $rankTotal));
            } else {
                // reaby disabled this, people doesn't like error messages
                // $this->exp_chatSendServerMessage($this->msg_noRank, $login, array());
            }
        }
    }

    public function chat_personalBest($login = null) {
        if ($login != null) {
            $record = $this->getCurrentChallangePlayerRecord($login);
            if (!$record) {
                // reaby disabed this, ppl doesn't like error messages!
                // $this->exp_chatSendServerMessage($this->msg_noPB, $login, array());
            } else {
                $time = \ManiaLive\Utilities\Time::fromTM($record->time);
                if (substr($time, 0, 2) === "0:") {
                    $time = substr($time, 2);
                }
                $avg = \ManiaLive\Utilities\Time::fromTM($record->avgScore);
                if (substr($avg, 0, 2) === "0:") {
                    $avg = substr($avg, 2);
                }
                if ($record->place > 0 && $record->place <= $this->config->recordsCount) {
                    $place = $record->place;
                } else {
                    $place = '--';
                }
                $this->exp_chatSendServerMessage($this->msg_personalBest, $login, array($time, $place, $avg, $record->nbFinish));
            }
        }
    }

    public function chat_forceSave($login) {
        $this->onEndMatch(array(), array());
        $this->exp_chatSendServerMessage($this->msg_admin_savedRecs, $login);
    }

    /**
     * Returns an array containing all the uid's of all the maps of the server
     */
    public function getUidArray() {
        $uids = array();
        foreach ($this->storage->maps as $map) {
            $uids[] = $map->uId;
        }
        return $uids;
    }

    /**
     * Returns a string to be used to in SQL to flter tracks
     * @return string
     */
    public function getUidSqlString() {
        $uids = "";
        foreach ($this->storage->maps as $map) {
            $uids .= $this->db->quote($map->uId) . ",";
        }
        return trim($uids, ",");
    }

}

?>
