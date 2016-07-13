<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords;

use ManiaLive\Event\Dispatcher;
use ManiaLive\Gui\ActionHandler;
use ManiaLive\Utilities\Console;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\Core\Events\ExpansionEvent;
use ManiaLivePlugins\eXpansion\Core\Events\ExpansionEventListener;
use ManiaLivePlugins\eXpansion\Core\i18n\Message;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\LocalRecords\Events\Event;
use ManiaLivePlugins\eXpansion\LocalRecords\Gui\Windows\Cps;
use ManiaLivePlugins\eXpansion\LocalRecords\Gui\Windows\Ranks;
use ManiaLivePlugins\eXpansion\LocalRecords\Gui\Windows\Records;
use ManiaLivePlugins\eXpansion\LocalRecords\Gui\Windows\Sector;
use ManiaLivePlugins\eXpansion\LocalRecords\Gui\Windows\TopSumsWindow;
use ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record;

abstract class LocalBase extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin implements ExpansionEventListener
{

    //This numbers are important do not change. THey are using binarie thinking.

    const DEBUG_NONE = 0; //00000

    const DEBUG_RECS_SAVE = 1; //00001

    const DEBUG_RECS_DB = 2; //00010

    const DEBUG_RECS_FULL = 3; //00011

    const DEBUG_RANKS = 4; //00100

    const DEBUG_LAPS = 8; //01000

    const DEBUG_RECPROCESSTIME = 16; //10000

    const DEBUG_ALL = 31; //11111currentMap

    const SCORE_TYPE_TIME = 'time';

    const SCORE_TYPE_SCORE = 'score';

    /**
     * Activating the debug mode of the plugin
     *
     * @var type int
     */
    protected $debug = self::DEBUG_NONE;

    /**
     * List of the records for the current track
     *
     * @var Record[] Array int => Record
     */
    protected $currentChallengeRecords = array();

    /**
     * The best times and other statistics of the current players on the server
     *
     * @var Record[] Array[$login] = Record
     */
    protected $currentChallengePlayerRecords = array();

    protected $currentChallangeSectorTimes = array();

    protected $currentChallangeSectorsCps = array();

    /**
     * Number of maps that was played since the plugin started
     *
     * @var int
     */
    protected $map_count = 0;

    /**
     * The current 100 best ranks in the server
     *
     * @var array int => login
     */
    protected $ranks = array();

    /**
     * The rank of players connected to the
     *
     * @var array login => int
     */
    protected $player_ranks = array();

    /**
     * Total amount of players that has a rank
     *
     * @var int
     */
    protected $total_ranks = -1;

    protected $ranks_reset = false;

    /**
     * Checking if we trued to get ranks before
     *
     * @var bool
     */
    protected $rank_needUpdated = false;

    /**
     * @var Config
     */
    protected $config;

    /**
     * All the messages need to be sent;
     *
     * @var Message
     */
    protected $msg_secure, $msg_new, $msg_improved, $msg_BeginMap, $msg_newMap, $msg_personalBest,
        $msg_noPB, $msg_showRank, $msg_noRank, $msg_secure_top1, $msg_secure_top5, $msg_new_top1,
        $msg_new_top5, $msg_improved_top1, $msg_improved_top5, $msg_admin_savedRecs, $msg_equals, $msg_equals_top5, $msg_equals_top1;

    protected $lastSave = 0;

    public static $txt_rank, $txt_nick, $txt_score, $txt_sector, $txt_cp, $txt_login,
        $txt_avgScore, $txt_nbFinish, $txt_wins, $txt_lastRec, $txt_ptime, $txt_nbRecords;

    public static $openSectorsAction = -1;

    public static $openRecordsAction = -1;

    public static $openCpsAction = -1;

    private $deleteTempLogin = null;

    public function expOnInit()
    {
        //Activating debug for records only
        $this->debug = self::DEBUG_NONE;

        LocalBase::$openSectorsAction = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(
            array($this, 'showSectorWindow')
        );
        LocalBase::$openRecordsAction = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(
            array($this, 'showRecsWindow')
        );
        LocalBase::$openCpsAction = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(
            array($this, 'showCpWindow')
        );

        $this->config = Config::getInstance();

        $this->setPublicMethod("getCurrentChallangePlayerRecord");
        $this->setPublicMethod("getPlayersRecordsForAllMaps");
        $this->setPublicMethod("getRecords");
        $this->setPublicMethod("getRanks");
        $this->setPublicMethod("getPlayerRank");
        $this->setPublicMethod("getTotalRanked");
        $this->setPublicMethod("showRecsWindow");
        $this->setPublicMethod("showRanksWindow");
        $this->setPublicMethod("showCpWindow");
        $this->setPublicMethod("showTopSums");

        //The Database plugin is needed.
        $this->addDependency(
            new \ManiaLive\PluginHandler\Dependency("\\ManiaLivePlugins\\eXpansion\Database\Database")
        );
    }

    public function eXpOnLoad()
    {

        //Recovering the multi language messages
        $this->msg_secure = eXpGetMessage($this->config->msg_secure);
        $this->msg_new = eXpGetMessage($this->config->msg_new);
        $this->msg_equals = eXpGetMessage($this->config->msg_equals);
        $this->msg_improved = eXpGetMessage($this->config->msg_improved);


        $this->msg_secure_top5 = eXpGetMessage($this->config->msg_secure_top5);
        $this->msg_new_top5 = eXpGetMessage($this->config->msg_new_top5);
        $this->msg_equals_top5 = eXpGetMessage($this->config->msg_equals_top5);
        $this->msg_improved_top5 = eXpGetMessage($this->config->msg_improved_top5);

        $this->msg_secure_top1 = eXpGetMessage($this->config->msg_secure_top1);
        $this->msg_new_top1 = eXpGetMessage($this->config->msg_new_top1);
        $this->msg_equals_top1 = eXpGetMessage($this->config->msg_equals_top1);
        $this->msg_improved_top1 = eXpGetMessage($this->config->msg_improved_top1);

        $this->msg_newMap = eXpGetMessage($this->config->msg_newMap);
        $this->msg_BeginMap = eXpGetMessage($this->config->msg_BeginMap);
        $this->msg_personalBest = eXpGetMessage($this->config->msg_personalBest);
        $this->msg_noPB = eXpGetMessage($this->config->msg_noPB);
        $this->msg_showRank = eXpGetMessage($this->config->msg_showRank);
        $this->msg_noRank = eXpGetMessage($this->config->msg_noRank);
        $this->msg_admin_savedRecs = eXpGetMessage('#admin_action#Records saved sucessfully into the database');

        self::$txt_rank = eXpGetMessage("#");
        self::$txt_nick = eXpGetMessage("NickName");
        self::$txt_score = eXpGetMessage("Score");
        self::$txt_sector = eXpGetMessage("Sector");
        self::$txt_cp = eXpGetMessage("CheckPoint Times");
        self::$txt_avgScore = eXpGetMessage("Average Score");
        self::$txt_nbFinish = eXpGetMessage("Finishes");
        self::$txt_wins = eXpGetMessage("Nb Wins");
        self::$txt_lastRec = eXpGetMessage("Last Rec Date");
        self::$txt_ptime = eXpGetMessage("Play Time");
        self::$txt_nbRecords = eXpGetMessage("nb Rec");
        self::$txt_login = eXpGetMessage("Login");

        $this->enableStorageEvents();
        $this->enableDedicatedEvents();
        $this->enableDatabase();
        $this->enableTickerEvent();
        Dispatcher::register(ExpansionEvent::getClass(), $this);

        //List of all records
        $cmd = $this->registerChatCommand("recs", "showRecsWindow", 0, true);
        $cmd->help = 'Show Records Window';

        $cmd = $this->registerChatCommand("topsums", "showTopSums", 0, true);
        $cmd->help = 'show Top Sums';

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

        $cmd = AdminGroups::addAdminCommand("delrec", $this, "chat_delRecord", "records_save");
        $cmd->setHelp("Deletes all records by login");

    }

    public function eXpOnReady()
    {
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
            $this->db->execute($q);
        }

        //Checking the version if the table
        $version = $this->callPublicMethod(
            '\ManiaLivePlugins\eXpansion\Database\Database', 'getDatabaseVersion', 'exp_records'
        );
        if (!$version) {
            $version = $this->callPublicMethod(
                '\ManiaLivePlugins\eXpansion\Database\Database', 'setDatabaseVersion', 'exp_records', 1
            );
        }

        $version = $this->callPublicMethod(
        '\ManiaLivePlugins\eXpansion\Database\Database', 'getDatabaseVersion', 'exp_records'
    );
        // update for version2
        if ($version == 1) {
            $q = "ALTER TABLE `exp_records` CHANGE `record_date` `record_date` INT( 12 ) NOT NULL;";
            $this->db->execute($q);
            $this->callPublicMethod('\ManiaLivePlugins\eXpansion\Database\Database', 'setDatabaseVersion', 'exp_records', 2);
        }

        $version = $this->callPublicMethod(
            '\ManiaLivePlugins\eXpansion\Database\Database', 'getDatabaseVersion', 'exp_records'
        );

        //Update for version 3
        if ($version <= 2) {
            try {
                $q = "ALTER TABLE `exp_records` ADD COLUMN score_type VARCHAR(10) DEFAULT 'time'";
                $this->db->execute($q);
            } catch (\Exception $e) {
                $this->console("[LocalRecords] There was error while updating database structure to version 3, setting version 3 as mostlikely it's already converted..");
            }
            $this->callPublicMethod(
                '\ManiaLivePlugins\eXpansion\Database\Database',
                'setDatabaseVersion',
                'exp_records',
                3
            );
        }


        if (!$this->db->tableExists("exp_ranks")) {
            $q = "CREATE TABLE `exp_ranks` (
                    `rank_playerlogin` VARCHAR( 30 ) NOT NULL DEFAULT '0',
                    `rank_rank` INT(6) NOT NULL DEFAULT '0',
                    `rank_challengeuid` VARCHAR( 27 ) NOT NULL DEFAULT '0',
                    `rank_nbLaps` INT( 3 ) NOT NULL,
                    KEY(`rank_challengeuid` ,  `rank_playerlogin` ,  `rank_nbLaps`)
                ) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = MYISAM ;";
            $this->db->execute($q);
            $this->resetRanks();
        }

        $this->onBeginMap("", "", "");
        if ($this->isPluginLoaded('eXpansion\Menu')) {
            $this->callPublicMethod('\ManiaLivePlugins\eXpansion\Menu', 'addSeparator', __('Records'), true);
            $this->callPublicMethod(
                '\ManiaLivePlugins\eXpansion\Menu', 'addItem', __('Map Records'), null, array($this, 'showRecsMenuItem'), false
            );
        }

        $this->getRanks();
    }

    public function onSettingsChanged(\ManiaLivePlugins\eXpansion\Core\types\config\Variable $var)
    {
        if ($var->getName() == 'resetRanks' && $var instanceof Boolean) {
            if ($var->getRawValue()) {
                $admins = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance();
                $admins->announceToPermission('expansion_settings', "#admin_action#Reseting and generating all server ranks...");
                $this->resetRanks();
                $var->setValue(false);
                $admins->announceToPermission('expansion_settings', "#admin_action#Server ranks reset done. Ranks generated : " . $this->config->nbMap_rankProcess);
            }
        }
    }

    public function showRecsMenuItem($login)
    {
        $this->showRecsWindow($login);
    }

    abstract protected function getScoreType();

    abstract public function formatScore($score);

    abstract protected function isBetterTime($newTime, $oldTime);

    abstract protected function secureBy($newTime, $oldTime);

    abstract protected function getDbOrderCriteria();

    abstract public function getNbOfLaps();

    /**
     * getPlayersRecordsForAllMaps($login)
     *
     * @param string $login
     *
     * @return array $list -> $list[mapuid] = (int) position
     */
    public function getPlayersRecordsForAllMaps($login)
    {

        $count = 0;
        $uids = "";
        foreach ($this->storage->maps as $map) {
            if (!isset($map->localRecords)) {
                $map->localRecords = array();
            }
            if (!isset($map->localRecords[$login])) {
                $count++;

                $uids .= $this->db->quote($map->uId) . ",";
                $mapsByUid[$map->uId] = $map;
            }
        }

        if ($count > 0) {
            $uids = trim($uids, ",");

            $q = ' SELECT `rank_rank` as rank,`rank_challengeuid` as uid '
                . ' FROM `exp_ranks` '
                . ' WHERE rank_challengeuid IN (' . $uids . ')'
                . '	AND rank_playerlogin = ' . $this->db->quote($login);
            $data = $this->db->execute($q);

            while ($row = $data->fetchObject()) {
                $mapsByUid[$row->uid]->localRecords[$login] = $row->rank;
            }
        }
    }

    public function onTick()
    {
        if ($this->config->saveRecFrequency != 0) {
            if ((time() - $this->lastSave) > ($this->config->saveRecFrequency * 60)) {
                $this->onEndMatch(array(), array());
                $this->lastSave = time();
            }
        }
    }


    public function onBeginMap($map, $warmUp, $matchContinuation)
    {
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

        if (($this->debug & self::DEBUG_LAPS) == self::DEBUG_LAPS) {
            Console::println("[DEBUG LocalRecs]Nb Laps : " . $nbLaps);
        }

        //Sending begin map messages
        if (sizeof($this->currentChallengeRecords) == 0 && $this->config->sendBeginMapNotices) {
            $this->eXpChatSendServerMessage(
                $this->msg_newMap, null, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->currentMap->name, 'wosnm'))
            );
        } else {
            if ($this->config->sendBeginMapNotices) {
                $time = $this->formatScore($this->currentChallengeRecords[0]->time);

                $this->eXpChatSendServerMessage(
                    $this->msg_BeginMap, null, array(\ManiaLib\Utils\Formatting::stripCodes(
                        $this->storage->currentMap->name, 'wosnm'
                    ), $time, \ManiaLib\Utils\Formatting::stripCodes($this->currentChallengeRecords[0]->nickName, 'wosnm'))
                );
                foreach ($this->storage->players as $login => $player) {
                    $this->chat_personalBest($login, null);
                }
                foreach ($this->storage->spectators as $login => $player) {
                    $this->chat_personalBest($login, null);
                }
            }
        }
        //send Ranking
        if ($this->config->sendRankingNotices) {
            foreach ($this->storage->players as $login => $player) {
                $this->chat_showRank($login);
            }

            foreach ($this->storage->spectators as $login => $player) {
                $this->chat_showRank($login);
            }
        }

        // Consider save done.
        $this->lastSave = time();
    }

    public function onEndMatch($rankings, $winnerTeamOrMap)
    {
        $cons = "";
        //Checking for lap constraints
        if ($this->useLapsConstraints()) {
            $nbLaps = $this->getNbOfLaps();
            $cons .= " AND rank_nbLaps = " . $this->getNbOfLaps();
        } else {
            $nbLaps = 1;
            $cons .= " AND rank_nbLaps = 1";
        }

        if (($this->debug & self::DEBUG_LAPS) == self::DEBUG_LAPS) {
            Console::println("[DEBUG LocalRecs]Nb Laps : " . $nbLaps);
        }

        $updated = false;

        //We update the database
        //Firs of the best records
        $currentMap = $this->storage->currentMap;
        foreach ($this->storage->maps as $map) {
            if ($map->uId == $this->storage->currentMap->uId) {
                $currentMap = $map;
                break;
            }
        }
        $currentMap->localRecords = array();
        foreach ($this->currentChallengeRecords as $i => $record) {
            $currentMap->localRecords[$record->login] = $record->place;
            $newUpdate = $this->updateRecordInDatabase($record, $nbLaps);
            $updated = $updated || $newUpdate;
        }
        //Now the rest of the times as well(PB)
        foreach ($this->currentChallengePlayerRecords as $i => $record) {
            $newUpdate = $this->updateRecordInDatabase($record, $nbLaps);
            $updated = $updated || $newUpdate;
        }

        if ($updated) {
            $this->updateRanks($this->storage->currentMap->uId, $nbLaps, true);
        } else {
            $q = "SELECT rank_playerlogin FROM `exp_ranks`"
                . " WHERE rank_challengeuid = " . $this->db->quote($this->storage->currentMap->uId) . $cons;
            $data = $this->db->execute($q);
            if (sizeof($data->fetchArray()) == 0) {
                $this->updateRanks($this->storage->currentMap->uId, $nbLaps, true);
            }
        }
    }

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap)
    {

        if ($this->ranks_reset) {
            $this->resetRanks();
        }

        // Added this to calulate the new ranks during every map change -reaby
        $this->rank_needUpdated = true;
        $this->getRanks();
    }

    public function onPlayerConnect($login, $isSpectator)
    {
        $uid = $this->storage->currentMap->uId;
        //If the player doesn't have a record get best time and other...
        $this->getFromDbPlayerRecord($login, $uid);

        //Send a message telling him about records on this map
        if (sizeof($this->currentChallengeRecords) == 0 && $this->config->sendBeginMapNotices) {
            $this->eXpChatSendServerMessage(
                $this->msg_newMap, $login, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->currentMap->name, 'wosnm'))
            );
        } else {
            if ($this->config->sendBeginMapNotices) {
                $time = $this->formatScore($this->currentChallengeRecords[0]->time);

                $this->eXpChatSendServerMessage(
                    $this->msg_BeginMap, $login, array(\ManiaLib\Utils\Formatting::stripCodes(
                        $this->storage->currentMap->name, 'wosnm'
                    ), $time, \ManiaLib\Utils\Formatting::stripCodes($this->currentChallengeRecords[0]->nickName, 'wosnm'))
                );
            }
        }

        //Get rank of the player
        if ($this->config->sendRankingNotices) {
            $this->chat_showRank($login);
        }
    }

    public function onPlayerDisconnect($login, $reason = null)
    {
        if (isset($this->player_ranks[$login]) && $this->player_ranks[$login] > 100) {
            //And rank data
            unset($this->player_ranks[$login]);
        }
    }

    public function onMapListModified($curMapIndex, $nextMapIndex, $isListModified)
    {
        //$this->ranks_reset = true;
    }

    /**
     * Will add a a record to the current map records buffer.
     * The record will only be save on endMap
     *
     * @param string $login the login of the player who did the time
     * @param int $score His score/time
     * @param int $gamemode The gamemode while he did the record
     * @param array() $cpScore list of CheckPoint times
     */
    public function addRecord($login, $score, $gamemode, $cpScore)
    {
        $uid = $this->storage->currentMap->uId;
        $player = $this->storage->getPlayerObject($login);
        $force = false;
        $this->currentChallangeSectorTimes = array();

        if (is_object($player) == false) {
            $this->console("[eXp] notice: Error while saving record for login '" . $login . "',couldn't fetch player object!");

            return;
        }

        //Player doesen't have record need to create one
        if (!isset($this->currentChallengePlayerRecords[$login])) {
            $record = new Record();
            $record->login = $login;
            $record->nickName = $player->nickName;
            $record->time = $score;
            $record->nbFinish = 1;
            $record->avgScore = $score;
            $record->gamemode = self::eXpGetCurrentCompatibilityGameMode();
            $record->nation = $player->path;
            $record->uId = $uid;
            $record->place = sizeof($this->currentChallengeRecords) + 1;
            $record->ScoreCheckpoints = $cpScore;
            $i = sizeof($this->currentChallengeRecords);
            if ($i > $this->config->recordsCount) {
                $i = $this->config->recordsCount;
            }
            $this->currentChallengeRecords[$i] = $record;
            $this->currentChallengePlayerRecords[$login] = $record;
            $this->currentChallengePlayerRecords[$login]->isNew = true;
            $force = true;
            if (($this->debug & self::DEBUG_RECS_SAVE) == self::DEBUG_RECS_SAVE) {
                $this->console("[eXp][DEBUG][LocalRecords:RECS]$login just did his firs time of $score on this map");
            }
        } else {
            //We update the old records average time and nbFinish
            $this->currentChallengePlayerRecords[$login]->nbFinish++;
            $avgScore = (($this->currentChallengePlayerRecords[$login]->nbFinish - 1) * $this->currentChallengePlayerRecords[$login]->avgScore + $score) / $this->currentChallengePlayerRecords[$login]->nbFinish;
            $this->currentChallengePlayerRecords[$login]->avgScore = $avgScore;

            if (($this->debug & self::DEBUG_RECS_SAVE) == self::DEBUG_RECS_SAVE) {
                $this->console(
                    "[eXp][DEBUG][LocalRecords:RECS]$login just did a new time of $score. His current rank is " . $this->currentChallengePlayerRecords[$login]->place
                );
            }
        }

        $nrecord = $this->currentChallengePlayerRecords[$login];

        //We flag it as it needs to be updated in the database as well
        $nrecord->isUpdated = true;

        //Now we need to find it's rank
        if ($force || $this->isBetterTime($score, $nrecord->time)) {

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
            if ($i >= $this->config->recordsCount) {
                $i = $this->config->recordsCount - 1;
            }

            if (($this->debug & self::DEBUG_RECS_FULL) == self::DEBUG_RECS_FULL) {
                $this->console(
                    "[eXp][DEBUG][LocalRecords:RECS]Starting to look for the rank of $login 's record at rank $i+1"
                );
            }

            $firstRecord = ($i < 0);

            //For each record worse then the new, push it back and push forward the new one
            while ($i >= 0 && !$this->isBetterTime($this->currentChallengeRecords[$i]->time, $nrecord->time)) {
                $record = $this->currentChallengeRecords[$i];

                if (($this->debug & self::DEBUG_RECS_FULL) == self::DEBUG_RECS_FULL) {
                    $this->console(
                        "[eXp][DEBUG][LocalRecords:RECS]$login is getting better : " . $nrecord->place . "=>" . ($nrecord->place - 1)
                        . "And " . $record->login . " is getting worse" . $record->place . "=>" . ($record->place + 1)
                    );
                }

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
            $nrecord->ScoreCheckpoints = $cpScore;
            if (($this->debug & self::DEBUG_RECS_SAVE) == self::DEBUG_RECS_SAVE) {
                $this->console(
                    "[eXp][DEBUG][LocalRecords:RECS]$login new rec Rank found" . $nrecord->place . " Old was : " . $recordrank_old
                );
            }

            //If relay don't send message, host server will send one.
            if ($this->expStorage->isRelay) {
                return;
            }

            /*
             * Found new Rank sending message
             */
            //Formating Time
            $time = $this->formatScore($nrecord->time);

            //No new rank, just drove a better time
            if ($nrecord->place == $recordrank_old && !$force && $nrecord->place <= $this->config->recordsCount) {
                $securedBy = $this->secureBy($nrecord->time, $recordtime_old);

                // equals time
                if ($nrecord->time == $recordtime_old) {
                    $msg = $this->msg_equals;
                    if ($nrecord->place <= 5) {
                        $msg = $this->msg_equals_top5;
                        if ($nrecord->place == 1) {
                            $msg = $this->msg_equals_top1;
                        }
                    }
                    if ($nrecord->place <= $this->config->recordPublicMsgTreshold) {
                        $this->eXpChatSendServerMessage(
                            $msg, null, array(\ManiaLib\Utils\Formatting::stripCodes(
                                $nrecord->nickName, 'wosnm'
                            ), $nrecord->place, $time)
                        );
                    } else {
                        $this->eXpChatSendServerMessage(
                            $msg, $login, array(\ManiaLib\Utils\Formatting::stripCodes(
                                $nrecord->nickName, 'wosnm'
                            ), $nrecord->place, $time)
                        );
                    }
                    // improves time
                } else {
                    $msg = $this->msg_secure;
                    if ($nrecord->place <= 5) {
                        $msg = $this->msg_secure_top5;
                        if ($nrecord->place == 1) {
                            $msg = $this->msg_secure_top1;
                        }
                    }
                    if ($nrecord->place <= $this->config->recordPublicMsgTreshold) {
                        $this->eXpChatSendServerMessage(
                            $msg, null, array(\ManiaLib\Utils\Formatting::stripCodes(
                                $nrecord->nickName, 'wosnm'
                            ), $nrecord->place, $time, $recordrank_old, $securedBy)
                        );
                    } else {
                        $this->eXpChatSendServerMessage(
                            $msg, $login, array(\ManiaLib\Utils\Formatting::stripCodes(
                                $nrecord->nickName, 'wosnm'
                            ), $nrecord->place, $time, $recordrank_old, $securedBy)
                        );
                    }
                }

                \ManiaLive\Event\Dispatcher::dispatch(
                    new Event(Event::ON_UPDATE_RECORDS, $this->currentChallengeRecords)
                );
            } //Improved time and new Rank
            else {
                if ($nrecord->place < $recordrank_old && !$force && $nrecord->place <= $this->config->recordsCount) {
                    $securedBy = $this->secureBy($nrecord->time, $recordtime_old);

                    $msg = $this->msg_improved;
                    if ($nrecord->place <= 5) {
                        $msg = $this->msg_improved_top5;
                        if ($nrecord->place == 1) {
                            $msg = $this->msg_improved_top1;
                        }
                    }

                    if ($nrecord->place <= $this->config->recordPublicMsgTreshold) {
                        $this->eXpChatSendServerMessage(
                            $msg, null, array(\ManiaLib\Utils\Formatting::stripCodes(
                                $nrecord->nickName, 'wosnm'
                            ), $nrecord->place, $time, $recordrank_old, $securedBy)
                        );
                    } else {
                        $this->eXpChatSendServerMessage(
                            $msg, $login, array(\ManiaLib\Utils\Formatting::stripCodes(
                                $nrecord->nickName, 'wosnm'
                            ), $nrecord->place, $time, $recordrank_old, $securedBy)
                        );
                    }

                    \ManiaLive\Event\Dispatcher::dispatch(
                        new Event(Event::ON_NEW_RECORD, $this->currentChallengeRecords, $nrecord)
                    );
                } //First record the player drove
                else {
                    if ($nrecord->place <= $this->config->recordsCount) {
                        $msg = $this->msg_new;
                        if ($nrecord->place <= 5) {
                            $msg = $this->msg_new_top5;
                            if ($nrecord->place == 1) {
                                $msg = $this->msg_new_top1;
                            }
                        }
                        if ($nrecord->place <= $this->config->recordPublicMsgTreshold) {
                            $this->eXpChatSendServerMessage(
                                $msg, null, array(\ManiaLib\Utils\Formatting::stripCodes(
                                    $nrecord->nickName, 'wosnm'
                                ), $nrecord->place, $time)
                            );
                        } else {
                            $this->eXpChatSendServerMessage(
                                $msg, $login, array(\ManiaLib\Utils\Formatting::stripCodes(
                                    $nrecord->nickName, 'wosnm'
                                ), $nrecord->place, $time)
                            );
                        }

                        \ManiaLive\Event\Dispatcher::dispatch(
                            new Event(Event::ON_NEW_RECORD, $this->currentChallengeRecords, $nrecord)
                        );
                    }
                }
            }
            \ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_PERSONAL_BEST, $nrecord));
        } else {
            \ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_NEW_FINISH, $login));
        }
    }

    /**
     * Will update the record in the database.
     *
     * @param Record $record
     * @param        $nbLaps
     *
     * @return bool Was the record updated in the database
     */
    protected function updateRecordInDatabase(Record $record, $nbLaps)
    {
        //If relay server host will save records no need to do it here
        if ($this->expStorage->isRelay) {
            return;
        }

        //print_r($record);
        //$uid = $this->storage->currentMap->uId;
        $uid = $record->uId;
        $changed = false;
        if ($record->isDelete) {
            $this->deleteRecordInDatabase($record, $nbLaps);

            return true;
        } else {
            if ($record->isNew) {
                //If the record is new we insert
                $q = 'INSERT INTO `exp_records` (`record_challengeuid`, `record_playerlogin`, `record_nbLaps`
                            ,`record_score`, `record_nbFinish`, `record_avgScore`, `record_checkpoints`, `record_date`
                            , `score_type`)
                        VALUES(' . $this->db->quote($uid) . ',
                            ' . $this->db->quote($record->login) . ',
                            ' . $this->db->quote($nbLaps) . ',
                            ' . $this->db->quote($record->time) . ',
                            ' . $this->db->quote($record->nbFinish) . ',
                            ' . $this->db->quote($record->avgScore) . ',
                            ' . $this->db->quote(implode(",", $record->ScoreCheckpoints)) . ',
                            ' . $this->db->quote($record->date) . ',
                            ' . $this->db->quote($this->getScoreType()) . '
                        )';
                $this->db->execute($q);
                $record->isNew = false;
                $changed = true;
            } else {
                if ($record->isUpdated) {
                    //If it isn't but it has been updated we update
                    $q = 'UPDATE `exp_records`
                        SET `record_score` = ' . $this->db->quote($record->time) . ',
                            `record_nbFinish` = ' . $this->db->quote($record->nbFinish) . ',
                            `record_avgScore` = ' . $this->db->quote($record->avgScore) . ',
                            `record_checkpoints` = ' . $this->db->quote(implode(",", $record->ScoreCheckpoints)) . ',
                            `record_date` = ' . $this->db->quote($record->date) . '
                        WHERE `record_challengeuid` = ' . $this->db->quote($uid) . '
                            AND `record_playerlogin` =  ' . $this->db->quote($record->login) . '
                            AND `record_nbLaps` = ' . $this->db->quote($nbLaps) . '
                            AND `score_type` = ' . $this->db->quote($this->getScoreType()) . ';';
                    $this->db->execute($q);
                    $changed = true;
                }
            }
        }
        //We flag it as updated
        $record->isUpdated = false;

        return $changed;
    }

    /**
     * @param Record $record Record to delete
     * @param int $nbLaps
     *
     * @return bool
     */
    protected function deleteRecordInDatabase(Record $record, $nbLaps)
    {
        try {
            $q = 'DELETE FROM `exp_records`
				WHERE record_challengeuid = ' . $this->db->quote($record->uId) . '
					AND record_playerlogin =' . $this->db->quote($record->login) . '
					AND record_nbLaps = ' . $this->db->quote($nbLaps) . '
					AND score_type = ' . $this->db->quote($this->getScoreType());

            $this->db->execute($q);
        } catch (\Exception $ex) {
            return false;
        }

        return true;
    }

    public function deleteRecordOfPlayerOnMap($adminLogin, $login)
    {
        if ($this->expStorage->isRelay) {
            $this->eXpChatSendServerMessage("#admin_error#Can't delete a record on Relay server.", $adminLogin);
        }

        if (isset($this->currentChallengePlayerRecords[$login])) {
            $record = $this->currentChallengePlayerRecords[$login];

            if ($record->isDelete) {
                $record->isDelete = false;
                $this->eXpChatSendServerMessage("#admin_action#Delete of record player canceled!", $adminLogin);
            } else {
                $this->eXpChatSendServerMessage("#admin_action#Record of player deleted! Restart map to take in account.", $adminLogin);
                $record->isDelete = true;
            }
        } else {
            $this->eXpChatSendServerMessage("#admin_error#Player doesn't have a record on this map! can't delete.", $adminLogin);
        }
    }

    /**
     * updateCurrentChallengeRecords()
     * Updates currentChallengePlayerRecords and the currentChallengeRecords arrays
     * with the current Challange Records.
     *
     * @return void
     */
    protected function updateCurrentChallengeRecords()
    {
        //If relay server host will save records no need to do it here
        if ($this->expStorage->isRelay) {
            return;
        }

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
        \ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_RECORDS_LOADED, $this->currentChallengeRecords));
    }

    /**
     * It will get the list of records of this map from the database
     *
     * @param mixed $gamemode
     *
     * @return Record[]
     */
    protected function buildCurrentChallangeRecords($gamemode = null)
    {
        $challenge = $this->storage->currentMap;

        if ($gamemode === null || $gamemode == '') {
            $gamemode = self::eXpGetCurrentCompatibilityGameMode();
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
                        AND `score_type` = " . $this->db->quote($this->getScoreType()) . "
                    ORDER BY  " . $this->getDbOrderCriteria() . "
                    LIMIT 0, " . $this->config->recordsCount . ";";

        $dbData = $this->db->execute($q);

        if ($dbData->recordCount() == 0) {
            return array();
        }

        $i = 1;
        $records = array();
        $players = array();
        while ($data = $dbData->fetchStdObject()) {

            $record = new Record();
            $this->currentChallengePlayerRecords[$data->record_playerlogin] = $record;

            $record->place = $i;
            $record->login = $data->record_playerlogin;
            $record->nickName = $data->player_nickname;
            $record->time = $data->record_score;
            $record->nbFinish = $data->record_nbFinish;
            $record->date = $data->record_date;
            $record->avgScore = $data->record_avgScore;
            $record->nation = $data->player_nation;
            $record->ScoreCheckpoints = explode(",", $data->record_checkpoints);
            $record->uId = $this->storage->currentMap->uId;

            if (isset($players[$record->login])) {
                $this->db->execute("DELETE FROM `exp_records` WHERE record_id = " . $data->record_id);
            } else {
                $records[$i - 1] = $record;
                $i++;
            }
        }

        return $records;
    }

    /**
     * deletes a record from database for map.
     *
     * @param \Maniaplanet\DedicatedServer\Structures\Map $challenge
     * @param string $login
     *
     * @return boolean
     */
    protected function deleteRecord(\Maniaplanet\DedicatedServer\Structures\Map $challenge, $login)
    {

        $q = "DELETE FROM `exp_records` WHERE `exp_records`.`record_challengeuid` = " . $this->db->quote(
                $challenge->uId
            ) . " and " .
            "`exp_records`.`record_playerlogin` = " . $this->db->quote($login) . ";";
        try {
            $this->db->execute($q);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * get topsums
     *
     * @return array["string login"] = array("stats" => array(0,1,2), total);
     *
     */
    public function getTopSums()
    {
        $q = 'SELECT `record_challengeuid`, GROUP_CONCAT(`record_playerlogin` ORDER BY `record_score` ASC) logins
			  FROM     `exp_records`
			  WHERE `record_challengeuid` IN (' . $this->getUidSqlString() . ')
			  GROUP BY  `record_challengeuid`';
        $sql = $this->db->execute($q);

        $topSums = array();

        foreach ($sql->fetchArrayOfObject() as $value) {
            $logins = explode(",", $value->logins);
            array_flip($logins);
            $logins = array_slice($logins, 0, 3);
            foreach ($logins as $index => $login) {
                if (!array_key_exists($login, $topSums)) {
                    $topSums[$login] = (object)array("stats" => Array(0 => 0, 1 => 0, 2 => 0), "total" => 0);
                }
                $topSums[$login]->stats[$index]++;
                $topSums[$login]->total++;
            }
        }

        $players = array_keys($topSums);
        $playerlist = "";

        foreach ($players as $player) {
            $playerlist .= $this->db->quote($player) . ",";
        }

        $q = "SELECT * FROM `exp_players` where `player_login` IN (" . trim($playerlist, ',') . ");";
        $sql = $this->db->execute($q);

        foreach ($sql->fetchArrayOfObject() as $obj) {
            $topSums[$obj->player_login]->{"nickName"} = $obj->player_nickname;
        }

        uasort($topSums, function ($a, $b) {
            if ($a->total == $b->total) {
                return 0;
            }

            return ($a->total > $b->total) ? -1 : 1;
        });

        return $topSums;
    }

    /**
     * getRecordsForMap().
     * gets the records for a map and returns array of record objects
     *
     * @param mixed $gamemode
     * @param Map $challenge
     * @param string $plugin
     *
     * @return array(Record)
     */
    public function getRecordsForMap($gamemode = null, $challenge = null, $plugin = null)
    {

        if ($challenge === null || $challenge == '') {
            $challenge = $this->storage->currentMap;
        }

        if ($gamemode === null || $gamemode == '') {
            $gamemode = self::eXpGetCurrentCompatibilityGameMode();
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
                        AND `score_type` = " . $this->db->quote($this->getScoreType()) . "
                    ORDER BY " . $this->getDbOrderCriteria() . "
                    LIMIT 0, " . $this->config->recordsCount . ";";

        $dbData = $this->db->execute($q);

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
            $record->date = $data->record_date;
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
     *
     * @return Record $record
     */
    protected function getFromDbPlayerRecord($login, $uId)
    {

        if (isset($this->currentChallengePlayerRecords[$login])) {
            return $this->currentChallengePlayerRecords[$login];
        }

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
                    AND `score_type` = " . $this->db->quote($this->getScoreType()) . "
                    " . $cons . ";";

        $dbData = $this->db->execute($q);
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

    public function getCurrentChallangePlayerRecord($login)
    {
        return isset($this->currentChallengePlayerRecords[$login]) ? $this->currentChallengePlayerRecords[$login] : null;
    }

    public function getRecords()
    {
        return $this->currentChallengeRecords;
    }

    /**
     * useLapsConstraints()
     * Helper function, checks game mode.
     *
     * @return int $laps
     */
    public function useLapsConstraints()
    {
        if (!$this->config->lapsModeCount1lap) {
            $gamemode = self::eXpGetCurrentCompatibilityGameMode();

            if ($gamemode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK || $gamemode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS || $gamemode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_ROUNDS || $gamemode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP) {
                $nbLaps = $this->getNbOfLaps();
                if ($nbLaps > 1) {
                    return $this->storage->currentMap->lapRace;
                }
            }
        }

        return false;
    }

    public function showTopSums($login)
    {
        TopSumsWindow::EraseAll();

        $win = TopSumsWindow::Create($login);
        $win->setTitle("TopSums");
        $win->setDatas($this->getTopSums());
        $win->setSize(100, 90);
        $win->show();
    }

    /**
     * showRecsWindow()
     *
     * Display a window for a login with best times
     *
     * @param type $login
     * @param \Maniaplanet\DedicatedServer\Structures\Map $map (optional)
     */
    public function showRecsWindow($login, $map = null)
    {
        Records::Erase($login);
        //try {
        if ($map === null) {
            $records = $this->currentChallengeRecords;
            $map = $this->storage->currentMap;
        } else {
            $records = $this->getRecordsForMap(null, $map);
        }
        $currentMap = false;
        if ($map == null || $map->uId == $this->storage->currentMap->uId) {
            $currentMap = true;
        }

        $window = Records::Create($login);
        /** @var Records $window */
        $window->setTitle(__('Records on a Map', $login));
        $window->centerOnScreen();
        $window->setSize(120, 100);
        $window->populateList($records, $this->config->recordsCount, $currentMap, $this);
        $window->show();
        /* } catch (\Exception $e) {
          $this->eXpChatSendServerMessage("Error: %s", $login, array($e->getMessage()));
          } */
    }

    /**
     * Will show a window with the 100 best ranked players
     *
     * @param $login
     */
    public function showRanksWindow($login)
    {
        Ranks::Erase($login);

        $window = Ranks::Create($login);
        $window->setTitle(__('Server Ranks', $login));
        $window->centerOnScreen();
        $window->populateList($this->getRanks(), 100);
        $window->setSize(150, 100);
        $window->show();
    }

    public function showCpWindow($login)
    {
        Cps::Erase($login);

        $window = Cps::Create($login);
        /** @var Cps $window */
        $window->setTitle(__('CheckPoints on Map', $login));
        $window->populateList($this->currentChallengeRecords, 100, $this);
        $window->setSize(200, 100);
        $window->centerOnScreen();
        $window->show();
    }

    public function showSectorWindow($login)
    {

        if (empty($this->currentChallangeSectorTimes)) {

            $secs = array();

            foreach ($this->currentChallengePlayerRecords as $rec) {
                for ($cpt = 0; $cpt < sizeof($this->currentChallangeSectorsCps); $cpt++) {
                    $currentIndex = $this->currentChallangeSectorsCps[$cpt] - 1;
                    $prevIndex = $cpt == 0 ? -1 : $this->currentChallangeSectorsCps[$cpt - 1] - 1;

                    if (isset($rec->ScoreCheckpoints[$currentIndex])) {
                        $old = ($prevIndex < 0) ? 0 : (isset($rec->ScoreCheckpoints[$prevIndex]) ? $rec->ScoreCheckpoints[$prevIndex] : 0);
                        $secs[$cpt][] = array('sectorTime' => $rec->ScoreCheckpoints[$currentIndex] - $old,
                            'recordObj' => $rec);
                    }
                }
            }

            $i = 0;
            foreach ($secs as $sec) {
                $this->currentChallangeSectorTimes[$i] = $this->array_sort($sec, 'sectorTime');
                $i++;
            }
        }

        $window = Sector::Create($login);
        /** @var Sector $window */
        $window->setTitle(__('Sector Times on Map', $login));
        $window->populateList($this->currentChallangeSectorTimes, 100, $this);
        $window->setSize(160, 100);
        $window->centerOnScreen();
        $window->show();
    }

    protected function calcCP($totalcps)
    {
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

    protected function array_sort($array, $on, $order = SORT_ASC)
    {
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
            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }

    /**
     * Ranks of all players online on the server
     *
     * @return array
     */
    public function getOnlineRanks()
    {
        return $this->player_ranks;
    }

    protected function resetRanks()
    {
        //If relay server host can't reset ranks
        if ($this->expStorage->isRelay) {
            return;
        }

        $fullStart = \ManiaLivePlugins\eXpansion\Helpers\Timer::startNewTimer("[LocalRecods]Reseting Ranks");
        $delete = \ManiaLivePlugins\eXpansion\Helpers\Timer::startNewTimer("[LocalRecods]Deleting existing Ranks");

        $this->db->execute('DELETE FROM exp_ranks WHERE rank_challengeuid IN (' . $this->getUidSqlString() . ')');
        $this->ranks = array();
        $this->player_ranks = array();
        \ManiaLivePlugins\eXpansion\Helpers\Timer::endTimer($delete, "[LocalRecods]Deleting existing Ranks");

        $i = 0;
        foreach ($this->storage->maps as $map) {
            $this->updateRanks($map->uId, 1, true);
            if ($i > $this->config->nbMap_rankProcess) {
                break;
            }
            $i++;
        }
        \ManiaLivePlugins\eXpansion\Helpers\Timer::endTimer($fullStart, "[LocalRecods]Reseting Ranks");
    }

    protected function updateRanks($challengeId, $nbLaps, $log = true)
    {
        //If relay server don't update ranks
        if ($this->expStorage->isRelay) {
            return;
        }

        $id = -1;
        if ($log) {
            $id = \ManiaLivePlugins\eXpansion\Helpers\Timer::startNewTimer(
                "[LocalRecods]Updating Ranks for $challengeId"
            );
        }

        $this->db->execute(
            'DELETE FROM exp_ranks
                                        WHERE rank_challengeuid = \'' . $challengeId . '\'
                                AND rank_nbLaps = ' . $nbLaps
        );

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
                ORDER BY ' . $this->getDbOrderCriteria() . '
                LIMIT 0, ' . $this->config->recordsCount;

        $this->db->execute($q);
        \ManiaLivePlugins\eXpansion\Helpers\Timer::endTimer($id, "[LocalRecods]Updating Ranks for $challengeId");
    }

    /**
     * The Total number of player ranked
     *
     * @return int
     */
    public function getTotalRanked()
    {
        if ($this->total_ranks == -1 && !$this->expStorage->isRelay) {
            $q = 'SELECT Count(*) as nbRanked
                    FROM exp_ranks
                    WHERE rank_challengeuid IN (' . $this->getUidSqlString() . ')'
                . ' GROUP BY rank_playerlogin'
                . ' HAVING COUNT(*) > 5';

            $data = $this->db->execute($q);

            if ($data->recordCount() == 0) {
                $this->total_ranks = -1;
            } else {
                $vals = $data->fetchStdObject();
                $this->total_ranks = $data->recordCount();
            }
        }

        return $this->total_ranks;
    }

    /**
     * Returns the players server rank as it is buffered.
     *
     * @param $login
     *
     * @return int
     */
    public function getPlayerRank_old($login)
    {
        $id = -1;
        foreach ($this->ranks as $id => $class) {
            if (!property_exists($class, "rank_playerlogin")) {
                return -1;
            }

            if ($class->rank_playerlogin == $login) {
                return $id + 1;
            }
        }

        return -1; // added failsafe
    }

    /**
     * Returns the players server rank as it is buffered.
     *
     * @param $login
     *
     * @return int
     */
    public function getPlayerRank($login)
    {
        if ($this->expStorage->isRelay) {
            return -1;
        }

        if (!isset($this->player_ranks[$login])) {

            $nbTrack = sizeof($this->storage->maps);
            $uids = $this->getUidSqlString();


            $q = 'SELECT ((SUM( rank_rank ) + (' . $nbTrack . ' - COUNT( * ) ) *' . $this->config->recordsCount . ')/' . $nbTrack . ') AS points,
                        COUNT(*) as nbFinish
                    FROM exp_ranks
                    WHERE rank_playerlogin = ' . $this->db->quote($login)
                . ' AND rank_challengeuid IN (' . $uids . ')';

            $data = $this->db->execute($q);

            if ($data->recordCount() == 0) {
                $this->player_ranks[$login] = -1;

                return -1;
            } else {
                $vals = $data->fetchStdObject();
                $points = $vals->points;

                if (empty($points) || $vals->nbFinish <= 5) {
                    $this->player_ranks[$login] = -1;

                    return -1;
                }
            }

            $q = 'SELECT rank_playerlogin as betters
                    FROM exp_ranks
                    WHERE rank_challengeuid IN (' . $uids . ')
                    GROUP BY rank_playerlogin
                    HAVING ((SUM(rank_rank) + (' . $nbTrack . ' - Count(*))*' . $this->config->recordsCount . ')/' . $nbTrack . ') < ' . $points . ''
                . 'AND Count(*) > 5';

            $data = $this->db->execute($q);

            if ($data->recordCount() == 0) {
                $this->player_ranks[$login] = 1;
            } else {

                $this->player_ranks[$login] = $data->recordCount() + 1;
            }
        }

        return $this->player_ranks[$login];
    }

    /**
     *  Updates the bufffer of the 100 best ranked players if needed
     *
     * @return array
     */
    public function getRanks()
    {
        if ((empty($this->ranks) || $this->rank_needUpdated) && !$this->expStorage->isRelay) {

            $this->console("[LocalRecods]Fetching Server Ranks from Database !");

            $this->rank_needUpdated = false;
            $this->total_ranks = -1;
            $this->getTotalRanked();

            $nbTrack = sizeof($this->storage->maps);
            $uids = $this->getUidSqlString();

            $q = 'SELECT rank_playerlogin as login,
                            ((SUM(rank_rank) + (' . $nbTrack . ' - Count(*))*' . $this->config->recordsCount . ')/' . $nbTrack . ') as tscore,                            
                            Count(1) as nbRecords,                         
                            ' . $nbTrack . ' as nbMaps
               FROM exp_ranks
               WHERE rank_challengeuid IN (' . $uids . ')                                       
               GROUP BY rank_playerlogin                            
               ORDER BY tscore ASC
               LIMIT 0,100';

            $dbData = $this->db->execute($q);

            $this->ranks = array();

            if ($dbData->recordCount() == 0) {
                return $this->ranks;
            }

            $tempranks = array();
            $loginlist = array();
            $i = 1;

            while ($data = $dbData->fetchStdObject()) {
                $tempranks[$data->login] = $data;
                $loginlist[] = $data->login;
                $this->player_ranks[$data->login] = $i++;
            }

            $this->console("[LocalRecods]Fetching Records from Database !");

            $q = 'SELECT record_playerlogin AS login,
                  SUM(record_nbFinish) as nbFinish,
                  MAX(record_date) AS lastRec 
                  FROM exp_records WHERE record_playerlogin IN (' . $this->getLoginSqlString($loginlist) . ') GROUP BY record_playerlogin LIMIT 0,100';

            $dbData = $this->db->execute($q);

            while ($data = $dbData->fetchStdObject()) {
                $tempranks[$data->login] = (object)array_merge((array)$tempranks[$data->login], (array)$data);
            }

            $this->console("[LocalRecods]Fetching Players from Database !");
            $q = 'SELECT player_login as login,
		  player_nickname,
          player_updated,
          player_wins,
          player_timeplayed,
          player_nation
          FROM exp_players WHERE player_login IN (' . $this->getLoginSqlString($loginlist) . ') LIMIT 0,100;';

            $dbData = $this->db->execute($q);
            while ($data = $dbData->fetchStdObject()) {
                $tempranks[$data->login] = (object)array_merge((array)$tempranks[$data->login], (array)$data);
            }

            $this->ranks = array_values($tempranks);
        }

        return $this->ranks;
    }

    /**
     * Chat message displaying rank of player
     */
    public function chat_showRank($login = null)
    {
        if ($login != null) {
            //$rank = $this->getPlayerRank($login);
            //if ($rank == -2) {
            $rank = $this->getPlayerRank($login);
            //}
            $rankTotal = $this->getTotalRanked();
            if ($rank > 0) {
                $this->eXpChatSendServerMessage($this->msg_showRank, $login, array($rank, $rankTotal));
            } else {
                // reaby disabled this, people doesn't like error messages
                // $this->eXpChatSendServerMessage($this->msg_noRank, $login, array());
            }
        }
    }

    public function chat_personalBest($login = null)
    {
        if ($login != null) {
            $record = $this->getCurrentChallangePlayerRecord($login);
            if (!$record) {
                // reaby disabed this, ppl doesn't like error messages!
                // $this->eXpChatSendServerMessage($this->msg_noPB, $login, array());
            } else {
                $time = $this->formatScore($record->time);
                $avg = $this->formatScore($record->avgScore);

                if ($record->place > 0 && $record->place <= $this->config->recordsCount) {
                    $place = $record->place;
                } else {
                    $place = '--';
                }

                $this->eXpChatSendServerMessage(
                    $this->msg_personalBest, $login, array($time, $place, $avg, $record->nbFinish)
                );
            }
        }
    }

    public function chat_forceSave($login)
    {
        $this->onEndMatch(array(), array());
        $this->eXpChatSendServerMessage($this->msg_admin_savedRecs, $login);
    }

    public function chat_delRecord($login, $playerLogin = array())
    {
        $playerLogin = array_shift($playerLogin);

        if (!$playerLogin) {
            $this->eXpChatSendServerMessage("This command takes a login as parameter, none entered");
            return;
        }

        $q = "SELECT * FROM `exp_records` WHERE `exp_records`.`record_playerlogin` = " . $this->db->quote($playerLogin) . ";";
        $ret = $this->db->execute($q);

        if ($ret->recordCount() == 0) {
            $this->eXpChatSendServerMessage("Can't delete records: Login %s has no records.", $login, array($playerLogin));
            return;
        }

        $ac = ActionHandler::getInstance();
        $action = $ac->createAction(array($this, "delRecs"), $playerLogin);

        Gui::showConfirmDialog($login, $action, "Delete all records by " . $playerLogin . "?");

    }

    public function delRecs($login, $playerLogin)
    {
        $q = "DELETE FROM `exp_records` WHERE `exp_records`.`record_playerlogin` = " . $this->db->quote($playerLogin) . ";";
        try {
            $this->db->execute($q);
            Gui::showNotice("All records by " . $playerLogin . " are now deleted\n Widgets and records will update at next map.", $login);
        } catch (\Exception $e) {
            Gui::showNotice("Error deleting records by " . $playerLogin, $login);
        }
    }


    /**
     * Returns an array containing all the uid's of all the maps of the server
     */
    public function getUidArray()
    {
        $uids = array();
        foreach ($this->storage->maps as $map) {
            $uids[] = $map->uId;
        }

        return $uids;
    }

    /**
     * Returns a string to be used to in SQL to flter tracks
     *
     * @return string
     */
    public function getUidSqlString()
    {
        $uids = "";
        foreach ($this->storage->maps as $map) {
            $uids .= $this->db->quote($map->uId) . ",";
        }

        return trim($uids, ",");
    }


    public function getLoginSqlString($logins)
    {
        $out = "";
        foreach ($logins as $login) {
            $out .= $this->db->quote($login) . ",";
        }

        return trim($out, ",");
    }


    public function eXp_onRestartStart()
    {
        $this->onEndMatch(array(), array());
    }

    public function eXp_onRestartEnd()
    {
    }


    public function eXpOnUnload()
    {
        Dispatcher::unregister(ExpansionEvent::ON_RESTART_START, $this);
        $this->onEndMatch(array(), array());
        Sector::EraseAll();
        Cps::EraseAll();
        Ranks::EraseAll();
        Records::EraseAll();
    }

}
