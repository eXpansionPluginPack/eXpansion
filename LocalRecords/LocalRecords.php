<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords;

use ManiaLive\Event\Dispatcher;
use ManiaLive\Utilities\Console;
use ManiaLivePlugins\eXpansion\Core\i18n\Message;
use \ManiaLivePlugins\eXpansion\LocalRecords\Config;
use \ManiaLivePlugins\eXpansion\LocalRecords\Events\Event;
use ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record;

class LocalRecords extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {
    //This numbers are important do not change. THey are using binarie thinking.

    const DEBUG_NONE = 0;
    const DEBUG_RECS_SAVE = 1;
    const DEBUG_RECS_DB = 2;
    const DEBUG_RECS_FULL = 3;
    const DEBUG_RANKS = 4;
    const DEBUG_LAPS = 8;
    const DEBUG_ALL = 15;

    /**
     * Activating the debug mode of the plugin
     * @var type int
     */
    private $debug;

    /**
     * List of the records for the current track
     *
     * @var type Array int => Record
     */
    private $currentChallengeRecords = array();

    /**
     * The best times and other statistics of the current players on the server
     *
     * @var type Array login => Record
     */
    private $currentChallengePlayerRecords = array();

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

    /**
     * The map on which the total_ranks was updated
     * @var int
     */
    private $mapnb_rank = 0;

    /**
     * Checking if we trued to get ranks beffore
     * @var bool
     */
    private $rank_firstGet = false;

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
    private $msg_secure, $msg_new, $msg_BeginMap, $msg_newMap, $msg_showRank, $msg_noRank;
    public static $txt_rank, $txt_nick, $txt_score, $txt_avgScore, $txt_nbFinish, $txt_wins, $txt_lastRec, $txt_ptime, $txt_nbRecords;

    function exp_onInit() {
        //Activating debug for records only
        $this->debug = self::DEBUG_RECS_FULL;

        //Listing the compatible Games
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_ROUNDS);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TIMEATTACK);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TEAM);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_CUP);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_LAPS);
        $this->config = Config::getInstance();

        //Recovering the multi language messages
        $this->msg_secure = exp_getMessage($this->config->msg_secure);
        $this->msg_new = exp_getMessage($this->config->msg_new);
        $this->msg_newMap = exp_getMessage($this->config->msg_newMap);
        $this->msg_BeginMap = exp_getMessage($this->config->msg_BeginMap);
        $this->msg_showRank = exp_getMessage($this->config->msg_showRank);
        $this->msg_noRank = exp_getMessage($this->config->msg_noRank);

        self::$txt_rank = exp_getMessage("#");
        self::$txt_nick = exp_getMessage("NickName");
        self::$txt_score = exp_getMessage("Score");
        self::$txt_avgScore = exp_getMessage("Average Score");
        self::$txt_nbFinish = exp_getMessage("Finishes");
        self::$txt_wins = exp_getMessage("Nb Wins");
        self::$txt_lastRec = exp_getMessage("Last Rec Date");
        self::$txt_ptime = exp_getMessage("Play Time");
        self::$txt_nbRecords = exp_getMessage("nb Rec");

        $this->setPublicMethod("getCurrentChallangePlayerRecord");
        $this->setPublicMethod("getRecords");
        $this->setPublicMethod("getRanks");
        $this->setPublicMethod("getPlayerRank");
        $this->setPublicMethod("getTotalRanked");

        $this->addDependency(new \ManiaLive\PluginHandler\Dependency("eXpansion\Database"));

        //Oliverde8 Menu
        if ($this->isPluginLoaded('oliverde8\HudMenu')) {
            Dispatcher::register(\ManiaLivePlugins\oliverde8\HudMenu\onOliverde8HudMenuReady::getClass(), $this);
        }
    }

    public function exp_onLoad() {

        parent::exp_onLoad();

        $this->enableStorageEvents();
        $this->enableDedicatedEvents();
        $this->enableDatabase();

        $this->registerChatCommand("recs", "showRecsWindow", 0, true);
        $this->registerChatCommand("top100", "showRanksWindow", 0, true);
        $this->registerChatCommand("rank", "chat_showRank", 0, true);
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

        //Creating Record Ranks View
        $version = $this->callPublicMethod('eXpansion\Database', 'getDatabaseVersion', 'exp_recordranks');

        if (!$version || !$this->db->tableExists('exp_recordranks') || $version < 2) {
            $version = $this->callPublicMethod('eXpansion\Database', 'setDatabaseVersion', 'exp_recordranks', 2);
            $this->exp_chatSendServerMessage('[eXp]Creating Ranks table, this might take some time...', null);
            echo '[eXpansion]Creating View ...' . "\n";
            $q = "CREATE or REPLACE VIEW exp_recordranks AS
                    SELECT COUNT( * ) AS record_rank, r1.record_playerlogin AS rank_playerlogin, r1.record_challengeuid AS rank_challengeuid
                    FROM exp_records r1, exp_records r2
                    WHERE r1.record_score > r2.record_score
                    AND r1.record_nbLaps = r2.record_nbLaps
                    AND r1.record_challengeuid = r2.record_challengeuid
                    GROUP BY r1.record_playerlogin, r1.record_challengeuid";
            $this->db->query($q);
            $this->exp_chatSendServerMessage('[eXp]Creating Ranks table, DONE !', null);
        }
        /*
         * AND 10 < ( SELECT count(*) FROM exp_records r3 WHERE r3.record_playerlogin = r1.record_playerlogin)
          AND r1.record_score < (SELECT MAX(r3.record_score) FROM exp_records r3 WHERE r1.record_challengeuid = r3.record_challengeuid LIMIT 0, 100)
         */
        //Forcing load for current map to happen
        $this->onBeginMap("", "", "");
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

        if($this->debug | self::DEBUG_LAPS == self::DEBUG_LAPS)
            echo "[DEBUG LocalRecs]Nb Laps : " . $nbLaps . "\n";

        //Sending begin map messages
        if (sizeof($this->currentChallengeRecords) == 0 && $this->config->sendBeginMapNotices) {
            $this->exp_chatSendServerMessage($this->msg_newMap, null, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->currentMap->name, 'wosnm')));
        } else if ($this->config->sendBeginMapNotices) {
            $this->exp_chatSendServerMessage($this->msg_BeginMap, null, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->currentMap->name, 'wosnm'), \ManiaLive\Utilities\Time::fromTM($this->currentChallengeRecords[0]->time), \ManiaLib\Utils\Formatting::stripCodes($this->currentChallengeRecords[0]->nickName, 'wosnm')));
        }
    }

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {

        //Checking for lap constraints
        $uid = $this->storage->currentMap->uId;
        if ($this->useLapsConstraints()) {
            $nbLaps = $this->getNbOfLaps();
        } else {
            $nbLaps = 1;
        }

        if($this->debug | self::DEBUG_LAPS == self::DEBUG_LAPS)
            echo "[DEBUG LocalRecs]Nb Laps : " . $nbLaps . "\n";

        //We update the database

        //Firs of the best records
        foreach ($this->currentChallengeRecords as $i => $record) {
            $this->updateRecordInDatabase($record, $nbLaps);
        }
        //Now the rest of the times as well(PB)
        foreach ($this->currentChallengePlayerRecords as $i => $record) {
            $this->updateRecordInDatabase($record, $nbLaps);
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
    }

    public function onPlayerConnect($login, $isSpectator) {
        $uid = $this->storage->currentMap->uId;
        //If the player doesn't have a record get best time and other...
        $this->getFromDbPlayerRecord($login, $uid);

        //Send a message telling him about records on this map
        if (sizeof($this->currentChallengeRecords) == 0 && $this->config->sendBeginMapNotices) {
            $this->exp_chatSendServerMessage($this->msg_newMap, $login, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->currentMap->name, 'wosnm')));
        } else if ($this->config->sendBeginMapNotices) {
            $this->exp_chatSendServerMessage($this->msg_BeginMap, $login, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->currentMap->name, 'wosnm'), \ManiaLive\Utilities\Time::fromTM($this->currentChallengeRecords[0]->time), \ManiaLib\Utils\Formatting::stripCodes($this->currentChallengeRecords[0]->nickName, 'wosnm')));
        }

        //Get rank of the player
        $this->player_ranks[$login] = $this->getPlayerRankDb($login);
        if ($this->config->sendRankingNotices) {
            $this->chat_showRank($login);
        }
    }

    public function onPlayerDisconnect($login) {
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
     * @param mixed $playerUid
     * @param mixed $login
     * @param mixed $timeOrScore
     * @return
     */
    public function onPlayerFinish($playerUid, $login, $timeOrScore) {

        //Checking for valid time
        if (isset($this->storage->players[$login]) && $timeOrScore > 0) {
            $gamemode = $this->storage->gameInfos->gameMode;

            //If laps mode we need to ignore. Laps has it's own end map event(end finish lap)
            if ($gamemode == \DedicatedApi\Structures\GameInfos::GAMEMODE_LAPS && $this->config->lapsModeCount1lap)//Laps mode has it own on Player finish event
                return;

            //We add the record to the buffer
            $this->addRecord($login, $timeOrScore, $gamemode, $this->checkpoints[$login]);
        }
        //We reset the checkPoints
        $this->checkpoints[$login] = array();
    }

    public function onPlayerFinishLap($player, $time, $checkpoints, $nbLap) {

        if ($this->config->lapsModeCount1lap && isset($this->storage->players[$player->login]) && $time > 0) {
            $gamemode = $this->storage->gameInfos->gameMode;

            /*if ($gamemode != \DedicatedApi\Structures\GameInfos::GAMEMODE_LAPS)//Laps mode has it own on Player finish event
                return;*/

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
            $record->place = sizeof($this->currentChallengeRecords) + 1;
            $record->ScoreCheckpoints = $cpScore;
            $this->currentChallengeRecords[sizeof($this->currentChallengeRecords)] = $record;
            $this->currentChallengePlayerRecords[$login] = $record;
            $this->currentChallengePlayerRecords[$login]->isNew = true;
            $force = true;
            if ($this->debug | self::DEBUG_RECS_SAVE)
                \ManiaLive\Utilities\Console::println("[eXp][DEBUG][LocalRecords:RECS]$login just did his firs time of $score on this map");
        } else {
            //We update the old records average time and nbFinish
            $this->currentChallengePlayerRecords[$login]->nbFinish++;
            $avgScore = (($this->currentChallengePlayerRecords[$login]->nbFinish - 1) * $this->currentChallengePlayerRecords[$login]->avgScore + $score ) / $this->currentChallengePlayerRecords[$login]->nbFinish;
            $this->currentChallengePlayerRecords[$login]->avgScore = $avgScore;

            if ($this->debug & self::DEBUG_RECS_SAVE == self::DEBUG_RECS_SAVE)
                \ManiaLive\Utilities\Console::println("[eXp][DEBUG][LocalRecords:RECS]$login just did a new time of $score. His current rank is " . $this->currentChallengePlayerRecords[$login]->place);
        }
        //We flag it as it needs to be updated in the database as well
        $this->currentChallengePlayerRecords[$login]->isUpdated = true;

        //Now we need to find it's rank
        if ($force || $this->currentChallengePlayerRecords[$login]->time > $score) {

            //Saving old rank and time
            $recordrank_old = $this->currentChallengePlayerRecords[$login]->place;
            $recordtime_old = $this->currentChallengePlayerRecords[$login]->time;

            //Updating tume with new time/score
            $this->currentChallengePlayerRecords[$login]->time = $score;

            $nrecord = $this->currentChallengePlayerRecords[$login];

            //Update the checkoints
            $nrecord->ScoreCheckpoints = $cpScore;
            //And the date on which the record was driven
            $nrecord->date = time();

            //Now we need to try and find a rank to the time
            $i = $recordrank_old - 2;

            //IF old rank was to bad to take in considaration. Let's try the worst record and see
            if ($i >= $this->config->recordsCount)
                $i = $this->config->recordsCount;

            if ($this->debug & self::DEBUG_RECS_FULL == self::DEBUG_RECS_FULL)
                \ManiaLive\Utilities\Console::println("[eXp][DEBUG][LocalRecords:RECS]Starting to look for the rank of $login 's record at rank $i+1");

            //For each record worse then the new, push it back and push forward the new one
            while ($i >= 0 && $this->currentChallengeRecords[$i]->time > $nrecord->time) {
                $record = $this->currentChallengeRecords[$i];

                if ($this->debug & self::DEBUG_RECS_FULL == self::DEBUG_RECS_FULL)
                    \ManiaLive\Utilities\Console::println("[eXp][DEBUG][LocalRecords:RECS]$login is getting better : ".$nrecord->place."=>".($nrecord->place-1)
                        ."And ".$record->login." is getting worse".$record->place."=>".($record->place-1));
                //New record takes old recs place
                $this->currentChallengeRecords[$i] = $nrecord;
                //and old takes new recs place
                $this->currentChallengeRecords[$i + 1] = $record;
                //Old record get's worse
                $record->place++;
                //new get's better
                $nrecord->place--;
                $i--;
            }

            if ($this->debug & self::DEBUG_RECS_SAVE == self::DEBUG_RECS_SAVE)
                \ManiaLive\Utilities\Console::println("[eXp][DEBUG][LocalRecords:RECS]$login new rec Rank found".$nrecord->place." Old was : ".$recordrank_old);

            //Found new Rank sending message
            if ($nrecord->place == $recordrank_old && !$force && $nrecord->place <= $this->config->recordsCount) {
                $this->exp_chatSendServerMessage($this->msg_secure, null, array(\ManiaLib\Utils\Formatting::stripCodes($nrecord->nickName, 'wosnm'), $nrecord->place, \ManiaLive\Utilities\Time::fromTM($nrecord->time), $recordrank_old, \ManiaLive\Utilities\Time::fromTM($nrecord->time - $recordtime_old)));
                \ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_UPDATE_RECORDS, $this->currentChallengeRecords));
            } else if ($nrecord->place <= $this->config->recordsCount) {
                $this->exp_chatSendServerMessage($this->msg_new, null, array(\ManiaLib\Utils\Formatting::stripCodes($nrecord->nickName, 'wosnm'), $nrecord->place, \ManiaLive\Utilities\Time::fromTM($nrecord->time), $recordrank_old, $recordtime_old));
                \ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_UPDATE_RECORDS, $this->currentChallengeRecords));
            }
            \ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_PERSONAl_BEST, $nrecord));
        }
    }

    /**
     * Will update the record in the database.
     * @param Record $record
     * @param $nbLaps
     */
    private function updateRecordInDatabase(Record $record, $nbLaps) {
        $uid = $this->storage->currentMap->uId;
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
        //We may wan't to reset total ranks to have a new value. More records more player with a server rank
        $this->resetTotalRanks();

        $this->currentChallengePlayerRecords = array(); //reset
        $this->currentChallengeRecords = array(); //reset
        $this->currentChallengeRecords = $this->buildCurrentChallangeRecords(); // fetch
        $uid = $this->storage->currentMap->uId;

        //Getting current players rank
        foreach ($this->storage->players as $login => $player) { // get players
            $this->getFromDbPlayerRecord($login, $uid);
            $this->player_ranks[$login] = $this->getPlayerRankDb($login);
        }

        foreach ($this->storage->spectators as $login => $player) { // get spectators
            $this->getFromDbPlayerRecord($login, $uid);
            $this->player_ranks[$login] = $this->getPlayerRankDb($login);
        }

        //Dispatch event
        \ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_UPDATE_RECORDS, $this->currentChallengeRecords));
        \ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_NEW_RECORD, $this->currentChallengeRecords));
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
            return;

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

            $record->place = -1;
            $record->login = $data->record_playerlogin;
            $record->nickName = $data->player_nickname;
            $record->time = $data->record_score;
            $record->nbFinish = $data->record_nbFinish;
            $record->avgScore = $data->record_avgScore;
            $record->date = $data->record_date;
            $record->nation = $data->player_nation;
            $record->ScoreCheckpoints = explode(",", $data->record_checkpoints);

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

            if ($gamemode == \DedicatedApi\Structures\GameInfos::GAMEMODE_TIMEATTACK
                    || $gamemode == \DedicatedApi\Structures\GameInfos::GAMEMODE_LAPS
                    || $gamemode == \DedicatedApi\Structures\GameInfos::GAMEMODE_ROUNDS
                    || $gamemode == \DedicatedApi\Structures\GameInfos::GAMEMODE_CUP) {
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
     * Will show a windows with the best records
     * @param $login
     */
    public function showRecsWindow($login) {
        Gui\Windows\Records::Erase($login);

        $window = Gui\Windows\Records::Create($login);
        $window->setTitle(__('Records on Current Map', $login));
        $window->centerOnScreen();
        $window->populateList($this->currentChallengeRecords, $this->config->recordsCount);
        $window->setSize(120, 100);
        $window->show();
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

    /**
     * Ranks of all players online on the server
     * @return array
     */
    public function getOnlineRanks() {
        return $this->player_ranks;
    }

    /**
     * re calculates the number of players that has a rank
     */
    private function resetTotalRanks() {

        if ($this->total_ranks <= 0
                || ($this->map_count % (($this->total_ranks / $this->config->totalRankProcessCoef) + 1)) == 0) {

            $uids = $this->getUidSqlString();
            $this->player_ranks = array();
            $q = 'SELECT COUNT(DISTINCT rank_playerlogin) as nbPlayer
                FROM exp_recordranks
                WHERE rank_challengeuid IN (' . $uids . ')';

            $dbData = $this->db->query($q);

            if ($dbData->recordCount() == 0) {
                $this->total_ranks = -1;
            } else {
                $data = $dbData->fetchStdObject();
                $this->total_ranks = $data->nbPlayer;
            }
        }
    }

    /**
     * The Total number of player ranked
     * @return int
     */
    public function getTotalRanked() {
        return $this->total_ranks;
    }

    /**
     * Returns the players server rank as it is buffered.
     *
     * @param $login
     * @return int
     */
    public function getPlayerRank($login) {
        if (isset($this->player_ranks[$login])) {
            return $this->player_ranks[$login];
        } else {
            return -2;
        }
    }

    /**
     * It recovers the server rank of the player
     * @param $login
     * @return int
     */
    public function getPlayerRankDb($login) {

        if (isset($this->player_ranks[$login])) {
            return $this->player_ranks[$login];
        } else {
            $uids = $this->getUidSqlString();

            $q = 'SELECT count(*) as nbRecs
                    FROM exp_recordranks
                    WHERE rank_challengeuid IN (' . $uids . ')
                        AND rank_playerlogin = ' . $this->db->quote($login) . '';
            $dbData = $this->db->query($q);

            if ($dbData->recordCount() == 0) {
                return -1;
            } else {
                $data = $dbData->fetchStdObject();
                if ($data->nbRecs == 0)
                    return -1;
                if ($data->nbRecs < 10)
                    return -1;
            }

            $q = 'SELECT count(*) as ranking FROM (
            SELECT COUNT(*)
            FROM exp_recordranks
            WHERE rank_challengeuid IN (' . $uids . ')
            GROUP BY rank_playerlogin
            HAVING  SUM(100 - record_rank) > (SELECT SUM(100 - record_rank) as points
                                                FROM exp_recordranks
                                                WHERE rank_challengeuid IN (' . $uids . ')
                                                    AND rank_playerlogin = ' . $this->db->quote($login) . '
                                                ORDER BY points DESC)) as temp';

            $dbData = $this->db->query($q);

            if ($dbData->recordCount() == 0) {
                return -1;
            } else {
                $data = $dbData->fetchStdObject();
                return $data->ranking + 1;
            }
        }
    }

    /**
     *  Updates the bufffer of the 100 best ranked players if needed
     * @return array
     */
    public function getRanks() {

        if (!$this->rank_firstGet || ($this->map_count - $this->mapnb_rank) % $this->config->nbMap_rankProcess == 0) {
            $this->mapnb_rank = $this->map_count - 1;
            $this->rank_firstGet = true;
            $ranks = array();

            $uids = $this->getUidSqlString();

            $q = 'SELECT SUM(100 - record_rank) as tscore,
                            rank_playerlogin,
                            SUM(record_nbFinish) as nbFinish,
                            Count(*) as nbRecords,
                            player_nickname,
                            player_nicknameStripped,
                            player_updated,
                            player_wins,
                            player_timeplayed,
                            player_nation,
                            MAX(record_date) as lastRec,
                            ' . sizeof($this->storage->maps) . ' as nbMaps
                    FROM exp_recordranks rr, exp_records r, exp_players p
                    WHERE rank_challengeuid IN (' . $uids . ')
                        AND rr.rank_playerlogin = r.record_playerlogin
                        AND r.record_playerlogin = p.player_login
                        AND rank_challengeuid = r.record_challengeuid
                    GROUP BY rank_playerlogin,
                                player_nickname,
                                player_nicknameStripped,
                                player_updated,
                                player_wins,
                                player_timeplayed,
                                player_nation
                    ORDER BY tscore DESC
                    LIMIT 0, 100';

            $dbData = $this->db->query($q);

            if ($dbData->recordCount() == 0) {

            } else {
                while ($data = $dbData->fetchStdObject()) {
                    $ranks[] = $data;
                    /* $player = $this->callPublicMethod('eXpansion\Database', 'getPlayer', $data->rank_playerlogin);
                      if ($player != null) {
                      $player->tscore = $data->points;
                      $ranks[] = $player;
                      } */
                }
            }
            $this->ranks = array();
            $this->ranks = $ranks;
            return $ranks;
        }
        return $this->ranks;
    }

    /**
     * Chat message displaying rank of player
     */
    public function chat_showRank ($login = null) {
        if ($login != null) {
            $rank = $this->getPlayerRank($login);
			if ($rank == -2) {
				$rank = $this->getPlayerRankDb($login);
			}
            $rankTotal = $this->getTotalRanked();
            if ($rank > 0) {
                $this->exp_chatSendServerMessage($this->msg_showRank, $login, array($rank, $rankTotal));
            } else {
                $this->exp_chatSendServerMessage($this->msg_noRank, $login, array());
            }
        }
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

    /* public static $players;
      private $records = array();
      private $lastRecord = null;
      private $config;

      function exp_onInit() {
      $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_ROUNDS);
      $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TIMEATTACK);
      $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TEAM);
      $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_CUP);

      $this->config = Config::getInstance();
      \ManiaLivePlugins\eXpansion\Core\ColorParser::getInstance()->registerCode("record", $this->config->color_record);
      \ManiaLivePlugins\eXpansion\Core\ColorParser::getInstance()->registerCode("record_variable", $this->config->color_record_variable);
      }

      function exp_onLoad() {
      $this->enableDatabase();
      $this->enableDedicatedEvents();
      $this->enablePluginEvents();
      $this->setPublicMethod("getRecords");
      $this->registerChatCommand("top100", "showRanks", 0, true);

      $this->registerChatCommand("save", "saveRecords", 0, true, \ManiaLive\Features\Admin\AdminGroup::get());
      $this->registerChatCommand("load", "loadRecords", 0, true, \ManiaLive\Features\Admin\AdminGroup::get());
      $this->registerChatCommand("reset", "resetRecords", 0, true, \ManiaLive\Features\Admin\AdminGroup::get());


      if (!$this->db->tableExists("exp_records")) {
      $this->db->execute('CREATE TABLE IF NOT EXISTS `exp_records` (
      `uid` varchar(50) NOT NULL,
      `mapname` text NOT NULL,
      `mapauthor` text NOT NULL,
      `records` text NOT NULL,
      PRIMARY KEY (`uid`)
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
      }
      }

      public function exp_onReady() {
      $this->syncPlayers();
      $this->loadRecords($this->storage->currentMap->uId);
      $this->reArrage();


      foreach ($this->storage->players as $player)
      $this->onPlayerConnect($player->login, false);
      foreach ($this->storage->spectators as $player)
      $this->onPlayerConnect($player->login, true);


      // $this->readRecords($this->storage->currentMap->uId);
      }

      public function resetRecords() {
      $this->records = array();
      $this->reArrage();
      }

      public function saveRecords() {
      $uid = $this->db->quote($this->storage->currentMap->uId);
      $mapname = $this->db->quote($this->storage->currentMap->name);
      $author = $this->db->quote($this->storage->currentMap->author);
      $json = $this->db->quote(json_encode($this->records));
      $query = "INSERT INTO exp_records (`uid`, `mapname`, `mapauthor`, `records` ) VALUES (" . $uid . "," . $mapname . "," . $author . "," . $json . ") ON DUPLICATE KEY UPDATE `records`=" . $json . ";";
      $this->db->execute($query);
      }

      public function loadRecords($uid) {
      $json = $this->db->query("SELECT `records` from exp_records where `uid`=" . $this->db->quote($uid) . ";")->fetchArray();
      $records = json_decode($json['records']);
      $outRecords = array();
      if (count($records) == 0) {
      $this->records = array();
      return;
      }
      foreach ($records as $login => $record)
      $outRecords[$login] = new Structures\Record($login, $record->time, $record->place);

      $this->records = $outRecords;
      }

      function reArrage($save = false) {
      \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::sortAsc($this->records, "time");
      $i = 0;
      $newrecords = array();
      foreach ($this->records as $record) {
      if (array_key_exists($record->login, $newrecords))
      continue;
      $record->place = ++$i;
      $newrecords[$record->login] = $record;
      }
      $this->records = array_slice($newrecords, 0, $this->config->recordsCount);
      $this->lastRecord = end($this->records);

      if ($save)
      $this->saveRecords();
      \ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_UPDATE_RECORDS, $this->records));
      }

      function getRecords($pluginId = null) {
      $data = $this->db->query("SELECT * from exp_records; ")->fetchArrayOfObject();
      $outArray = array();
      foreach ($data as $record) {
      $outArray[$record->uid] = json_decode($record->records);
      }
      return $outArray;
      }

      function onBeginMap($map, $warmUp, $matchContinuation) {
      $this->loadRecords($this->storage->currentMap->uId);
      $this->reArrage();
      }

      function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {
      $this->saveRecords();
      }

      function onPlayerFinish($playerUid, $login, $time) {
      if ($time == 0)
      return;

      $x = 0;

      // if no records, make entry
      if (count($this->records) == 0) {
      $this->records[$login] = new Structures\Record($login, $time);
      \ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_NEW_RECORD, $this->records[$login]));
      $this->reArrage(false);
      $this->announce($login);
      }

      // so if the time is better than the last entry or the count of records is less than 20...
      if ($this->lastRecord->time > $time || count($this->records) < $this->config->recordsCount) {
      // if player exists on the list... see if he got better time
      if (array_key_exists($login, $this->records)) {
      if ($this->records[$login]->time > $time) {
      $oldRecord = $this->records[$login];
      $this->records[$login] = new Structures\Record($login, $time);
      \ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_NEW_RECORD, $this->records[$login]));
      $this->reArrage(false);
      $this->announce($login, $oldRecord);

      return;
      }
      // if not then just do a update for the time
      } else {
      $this->records[$login] = new Structures\Record($login, $time);
      \ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_NEW_RECORD, $this->records[$login]));
      $this->reArrage(false);
      $this->announce($login);

      return;
      }
      }
      }

      function announce($login, $oldRecord = null) {
      try {
      $player = $this->storage->getPlayerObject($login);
      if ($this->records[$login]->place == 1)
      $actionColor = '$FF0';

      $suffix = "th";
      $grats = __("a new record: ");
      switch ($this->records[$login]->place) {
      case 1:
      $suffix = "st";
      $grats = __('$o$03CC$04Co$06Dn$07Dg$08Er$09Ea$0BFt$0CFu$0CFl$1DFa$2DFt$3EFi$4EFo$5FFn$6FFs!$z$s', $login);
      break;
      case 2:
      $suffix = "nd";
      $grats = __('$o$F00W$F20e$F40l$F60l$F80 $F90D$FB0o$FD0n$FF0e!$z$s', $login);

      break;
      case 3:
      $suffix = "rd";
      $grats = __('$o$090G$0A0o$0B0od$0C0 $0D0G$0E0am$0F0e!$z$s', $login);
      break;
      }

      if ($oldRecord !== null) {
      $diff = \ManiaLive\Utilities\Time::fromTM($this->records[$login]->time - $oldRecord->time, true);
      $this->exp_chatSendServerMessage($grats . '#record_variable#$o %s$o%s #record#for#record_variable# %s $z$s#record#with a time of$o#record_variable# %s $o#record#$n(%s)', null, array($this->records[$login]->place, $suffix, \ManiaLib\Utils\Formatting::stripCodes($player->nickName, "wos"), \ManiaLive\Utilities\Time::fromTM($this->records[$login]->time), $diff));
      return;
      }

      $this->exp_chatSendServerMessage($grats . '#record_variable#$o %s$o%s #record#for#record_variable# %s $z$s#record#with a time of$o#record_variable# %s', null, array($this->records[$login]->place, $suffix, \ManiaLib\Utils\Formatting::stripCodes($player->nickName, "wos"), \ManiaLive\Utilities\Time::fromTM($this->records[$login]->time)));
      } catch (\Exception $e) {
      \ManiaLive\Utilities\Console::println("Error: couldn't show localrecords message" . $e->getMessage());
      }
      }

      function showRanks($login) {
      // @var array("Uid" => array(Structures\Record))
      $records = $this->getRecords();


      $ranks = array();
      $nbrec = array();
      $top3 = array();

      $maps = array();
      foreach ($this->storage->maps as $map) {
      $maps[] = $map->uId;
      }

      foreach ($records as $uid => $record) {
      if (in_array($uid, $maps)) {
      foreach ($record as $player) {
      if (!array_key_exists($player->login, $ranks))
      $ranks[$player->login] = 0;
      if (!array_key_exists($player->login, $top3))
      $top3[$player->login] = 0;
      if (!array_key_exists($player->login, $nbrec))
      $nbrec[$player->login] = array("count" => 0, "1" => 0, "2" => 0, "3" => 0);

      $ranks[$player->login] += $this->config->recordsCount - $player->place;

      $nbrec[$player->login]['count']++;
      if ($player->place == 1) {
      $nbrec[$player->login]['1']++;
      $top3[$player->login] += 3;
      }
      if ($player->place == 2) {
      $nbrec[$player->login]['2']++;
      $top3[$player->login] += 2;
      }
      if ($player->place == 2) {
      $nbrec[$player->login]['3']++;
      $top3[$player->login] += 1;
      }
      }
      }
      }

      Gui\Windows\RanksWindow::$ranks = $ranks;
      Gui\Windows\RanksWindow::$nbrec = $nbrec;
      Gui\Windows\RanksWindow::$top3 = $top3;

      $window = Gui\Windows\RanksWindow::Create($login);
      $window->setSize(130, 90);
      $window->centerOnScreen();
      $window->show();
      }

      function syncPlayers() {
      //$db = $this->db->query("Select * FROM exp_players")->fetchArrayOfAssoc();
      //foreach ($db as $array)
      // self::$players[$array['login']] = \ManiaLivePlugins\eXpansion\LocalRecords\Structures\DbPlayer::fromArray($array);
      }

      function onPlayerConnect($login, $isSpectator) {
      //$player = new \ManiaLivePlugins\eXpansion\LocalRecords\Structures\DbPlayer();
      //$player->fromPlayerObj($this->storage->getPlayerObject($login));
      //$this->db->execute($player->exportToDb());
      self::$players[$login] = $player;
      }

      function onPlayerDisconnect($login) {
      //unset(self::$players[$login]);
      } */
}

?>
