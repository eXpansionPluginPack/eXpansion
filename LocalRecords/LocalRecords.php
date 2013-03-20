<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords;

use ManiaLive\Event\Dispatcher;
use \ManiaLivePlugins\eXpansion\LocalRecords\Config;
use \ManiaLivePlugins\eXpansion\LocalRecords\Events\Event;
use ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record;

class LocalRecords extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $currentChallengeRecords = array();
    private $currentChallengePlayerRecords = array();
    private $checkpoints = array();
    private $config;
    private $msg_secure, $msg_new, $msg_BeginMap, $msg_newMap;
    public static $txt_rank, $txt_nick, $txt_score, $txt_avgScore, $txt_nbFinish, $txt_wins, $txt_lastRec;

    function exp_onInit() {
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_ROUNDS);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TIMEATTACK);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TEAM);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_CUP);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_LAPS);
        $this->config = Config::getInstance();

        $this->msg_secure = exp_getMessage('#variable#%1$s #record#secured his/her #rank#%2$s #record#. Local Record with time of #rank#%3$s #record#$n(-%5$s)');
        $this->msg_new = exp_getMessage('#variable#%1$s #record#gained the #rank#%2$s #record#. Local Record with time of #rank#%3$s');
        $this->msg_newMap = exp_getMessage('#variable#%1$s #record#Is a new Map. Currently no record!');
        $this->msg_BeginMap = exp_getMessage('#record#Current record on #variable#%1$s #record#is #variable#%2$s #record#by #variable#%3$s');

        self::$txt_rank = exp_getMessage("#");
        self::$txt_nick = exp_getMessage("NickName");
        self::$txt_score = exp_getMessage("Score");
        self::$txt_avgScore = exp_getMessage("Average Score");
        self::$txt_nbFinish = exp_getMessage("Nb Finishes");
        self::$txt_wins = exp_getMessage("Nb Wins");
        self::$txt_lastRec = exp_getMessage("Last Rec Date");

        $this->setPublicMethod("getCurrentChallangePlayerRecord");
        $this->setPublicMethod("getRecords");

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
    }

    public function exp_onReady() {
        parent::exp_onReady();
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
        $this->onBeginMap("", "", "");
    }

    public function onBeginMap($map, $warmUp, $matchContinuation) {
        $this->updateCurrentChallengeRecords();

        if (sizeof($this->currentChallengeRecords) == 0 && $this->config->sendBeginMapNotices) {
            $this->exp_chatSendServerMessage($this->msg_newMap, null, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->currentMap->name, 'wos')));
        } else if ($this->config->sendBeginMapNotices) {
            $this->exp_chatSendServerMessage($this->msg_BeginMap, null, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->currentMap->name, 'wos'), \ManiaLive\Utilities\Time::fromTM($this->currentChallengeRecords[0]->time), \ManiaLib\Utils\Formatting::stripCodes($this->currentChallengeRecords[0]->nickName, 'wos')));
        }
    }

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {
        $uid = $this->storage->currentMap->uId;
        $nbLaps = $this->getNbOfLaps();
        foreach ($this->currentChallengeRecords as $i => $record) {
            $this->updateRecordInDatabase($record, $nbLaps);
        }
        foreach ($this->currentChallengePlayerRecords as $i => $record) {
            $this->updateRecordInDatabase($record, $nbLaps);
        }
    }

    public function onPlayerConnect($login, $isSpectator) {
        $uid = $this->storage->currentMap->uId;
        $this->getFromDbPlayerRecord($login, $uid);
        if (sizeof($this->currentChallengeRecords) == 0 && $this->config->sendBeginMapNotices) {
            $this->exp_chatSendServerMessage($this->msg_newMap, $login, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->currentMap->name, 'wos')));
        } else if ($this->config->sendBeginMapNotices) {
            $this->exp_chatSendServerMessage($this->msg_BeginMap, $login, array(\ManiaLib\Utils\Formatting::stripCodes($this->storage->currentMap->name, 'wos'), \ManiaLive\Utilities\Time::fromTM($this->currentChallengeRecords[0]->time), \ManiaLib\Utils\Formatting::stripCodes($this->currentChallengeRecords[0]->nickName, 'wos')));
        }
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

        if (isset($this->storage->players[$login]) && $timeOrScore > 0) {
            $gamemode = $this->storage->gameInfos->gameMode;

            if ($gamemode == \DedicatedApi\Structures\GameInfos::GAMEMODE_LAPS && $this->config->lapsModeCount1lap)//Laps mode has it own on Player finish event
                return;

            $this->addRecord($login, $timeOrScore, $gamemode, $this->checkpoints[$login]);
        }
        $this->checkpoints[$login] = array();
    }

    public function onPlayerFinishLap($player, $time, $checkpoints, $nbLap) {

        if ($this->config->lapsModeCount1lap && isset($this->storage->players[$player->login]) && $time > 0) {
            $gamemode = $this->storage->gameInfos->gameMode;

            if ($gamemode != \DedicatedApi\Structures\GameInfos::GAMEMODE_LAPS)//Laps mode has it own on Player finish event
                return;

            $this->addRecord($login, $timeOrScore, $gamemode, $this->checkpoints[$login]);
            $this->checkpoints[$login] = array();
        }
    }

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
    }

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
        } else {
            $this->currentChallengePlayerRecords[$login]->nbFinish++;
            $avgScore = (($this->currentChallengePlayerRecords[$login]->nbFinish - 1) * $this->currentChallengePlayerRecords[$login]->avgScore + $score ) / $this->currentChallengePlayerRecords[$login]->nbFinish;
            $this->currentChallengePlayerRecords[$login]->avgScore = $avgScore;
        }
        $this->currentChallengePlayerRecords[$login]->isUpdated = true;

        //Now we need to find it's rank
        if ($force || $this->currentChallengePlayerRecords[$login]->time > $score) {

            $recordrank_old = $this->currentChallengePlayerRecords[$login]->place;
            $recordtime_old = $this->currentChallengePlayerRecords[$login]->time;
            $this->currentChallengePlayerRecords[$login]->time = $score;
            $nrecord = $this->currentChallengePlayerRecords[$login];
            $nrecord->ScoreCheckpoints = $cpScore;
            $nrecord->date = time();

            $i = $recordrank_old - 2;
            if ($i >= $this->config->recordsCount)
                $i = $this->config->recordsCount;

            while ($i >= 0 && $this->currentChallengeRecords[$i]->time > $nrecord->time) {
                $record = $this->currentChallengeRecords[$i];
                $this->currentChallengeRecords[$i] = $nrecord;
                $this->currentChallengeRecords[$i + 1] = $record;
                $record->place++;
                $nrecord->place--;
                $i--;
            }

            //Found new Rank
            if ($nrecord->place == $recordrank_old && !$force && $nrecord->place <= $this->config->recordsCount) {
                $this->exp_chatSendServerMessage($this->msg_secure, null, array(\ManiaLib\Utils\Formatting::stripCodes($nrecord->nickName, 'wos'), $nrecord->place, \ManiaLive\Utilities\Time::fromTM($nrecord->time), $recordrank_old, \ManiaLive\Utilities\Time::fromTM($nrecord->time - $recordtime_old)));
                \ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_UPDATE_RECORDS, $this->currentChallengeRecords));
            } else if ($nrecord->place <= $this->config->recordsCount) {
                $this->exp_chatSendServerMessage($this->msg_new, null, array(\ManiaLib\Utils\Formatting::stripCodes($nrecord->nickName, 'wos'), $nrecord->place, \ManiaLive\Utilities\Time::fromTM($nrecord->time), $recordrank_old, $recordtime_old));
                \ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_UPDATE_RECORDS, $this->currentChallengeRecords));
            }
            \ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_PERSONAl_BEST, $nrecord));
        }
    }

    private function updateRecordInDatabase(Record $record, $nbLaps) {
        $uid = $this->storage->currentMap->uId;
        if ($record->isNew) {
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

        $this->currentChallengePlayerRecords = array(); //reset
        $this->currentChallengeRecords = array(); //reset
        $this->currentChallengeRecords = $this->buildCurrentChallangeRecords(); // fetch
        $uid = $this->storage->currentMap->uId;
        foreach ($this->storage->players as $login => $player) { // get players
            $this->getFromDbPlayerRecord($login, $uid);
        }

        foreach ($this->storage->spectators as $login => $player) { // get spectators
            $this->getFromDbPlayerRecord($login, $uid);
        }
        \ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_UPDATE_RECORDS, $this->currentChallengeRecords));
        \ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_NEW_RECORD, $this->currentChallengeRecords));
    }

    /**
     * buildCurrentChallangeRecords().
     * This function will built the currentChallengePlayerRecords
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

            if ($gamemode == \ManiaLive\DedicatedApi\Structures\GameInfos::GAMEMODE_TIMEATTACK
                    || $gamemode == \ManiaLive\DedicatedApi\Structures\GameInfos::GAMEMODE_LAPS
                    || $gamemode == \ManiaLive\DedicatedApi\Structures\GameInfos::GAMEMODE_STUNTS
                    || $gamemode == \ManiaLive\DedicatedApi\Structures\GameInfos::GAMEMODE_CUP) {
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
                    return $this->storage->currentMap->lapRace;
            case \DedicatedApi\Structures\GameInfos::GAMEMODE_TEAM:
            case \DedicatedApi\Structures\GameInfos::GAMEMODE_CUP:
                return $this->storage->gameInfos->roundsForcedLaps;
                break;

            case \DedicatedApi\Structures\GameInfos::GAMEMODE_LAPS:
                return $this->storage->gameInfos->lapsNbLaps;
                break;

            default:
                return 1;
        }
    }

    public function showRecsWindow($login) {
        Gui\Windows\Records::Erase($login);

        $window = Gui\Windows\Records::Create($login);
        $window->setTitle(__('Records on Current Map', $login));
        $window->centerOnScreen();
        $window->populateList($this->currentChallengeRecords, $this->config->recordsCount);
        $window->setSize(120, 100);
        $window->show();
    }

    public function showRanksWindow($login){
        Gui\Windows\Ranks::Erase($login);

        $window = Gui\Windows\Ranks::Create($login);
        $window->setTitle(__('Server Ranks', $login));
        $window->centerOnScreen();
        $window->populateList($this->getRanks(), 100);
        $window->setSize(120, 100);
        $window->show();
    }


    public function getRanks(){

        $ranks2 = array();

       foreach($this->storage->maps as $map){

            $q = 'SELECT record_playerlogin,
                    ( (SELECT count(*) FROM exp_records WHERE record_challengeuid = '.$this->db->quote($map->uId).')
                        -
                    (SELECT count(*)  FROM exp_records r2 WHERE record_challengeuid = '.$this->db->quote($map->uId).'
                        AND r2.record_score < r1.record_score)
                    )as ranking
                    FROM exp_records r1, exp_players p
                    WHERE record_challengeuid = '.$this->db->quote($map->uId).'
                        AND r1.record_playerlogin = p.player_login
                    GROUP BY record_playerlogin, player_nickname, player_wins
                    ORDER BY ranking ASC
                    LIMIT 0 , 100 ';
           $dbData = $this->db->query($q);

            if ($dbData->recordCount() == 0) {

            }else{
                 while ($data = $dbData->fetchStdObject()) {
                    if(!isset($ranks2[$data->record_playerlogin]))
                        $ranks2[$data->record_playerlogin] = 0;
                    $ranks2[$data->record_playerlogin] += $data->ranking;
                }
            }
        }

        arsort($ranks2);
        $ranks = array();

        $i = 0;
        foreach ($ranks2 as $login => $rec) {
            $player = $this->callPublicMethod('eXpansion\Database', 'getPlayer', $login);
            if($player != null){
                $player->tscore = $rec;
                $ranks[] = $player;
            }
            $i++;
        }
        return $ranks;
        /*
        $uids = $this->getUidSqlString();

        $q = '
           SELECT record_playerlogin, player_nickname, player_wins, (
                                ((SELECT count(*)
                                FROM exp_records
                                WHERE record_challengeuid IN ('.$uids.') )*2
                                -
                                (SELECT count(*)
                                FROM exp_records r2
                                WHERE r2.record_challengeuid IN ('.$uids.')
                                    AND r2.record_score < r1.record_score))
                            )as ranking,
                            (SELECT SUM(record_nbFinish) FROM exp_records
                                WHERE record_challengeuid IN ('.$uids.')
                                    AND record_playerlogin = r1.record_playerlogin) as nbFinishes,
                            MAX(record_date) as last_record
            FROM exp_records r1,  exp_players p
            WHERE record_challengeuid IN ('.$uids.')
            AND r1.record_playerlogin = p.player_login
            GROUP BY record_playerlogin, player_nickname, player_wins
            ORDER BY ranking DESC
            LIMIT 0 , 100
        ';

        echo $q;

        $dbData = $this->db->query($q);

        if ($dbData->recordCount() == 0) {
            return array();
        }
        $ranks = array();
        $i = 1;
        while ($data = $dbData->fetchStdObject()) {
            $data->rank = $i;
            $ranks[] = $data;
        }
        return $ranks;/**/
    }


    public function getUidArray(){
        $uids = array();
        foreach($this->storage->maps as $map){
            $uids[] = $map->uId;
        }
    }

    public function getUidSqlString(){
        $uids = "";
        foreach($this->storage->maps as $map){
            $uids .= $this->db->quote($map->uId).",";
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
