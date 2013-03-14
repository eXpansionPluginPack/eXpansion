<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords;

use \ManiaLivePlugins\eXpansion\LocalRecords\Config;
use \ManiaLivePlugins\eXpansion\LocalRecords\Events\Event;
use ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record;

class LocalRecords extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $currentChallengeRecords = array();
    private $currentChallengePlayerRecords = array();
    private $checkpoints = array();

    private $msg_secure, $msg_new;
    
    function exp_onInit() {
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_ROUNDS);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TIMEATTACK);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TEAM);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_CUP);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_LAPS);
        $this->config = Config::getInstance();
        
        $this->msg_secure = exp_getMessage('#variable#%1$s #record#secured his/her #rank#%2$s #record#. Local Record!');
        $this->msg_new = exp_getMessage('#variable#%1$s #record#gained the #rank#%2$s #record#. Local Record!');
        
        $this->setPublicMethod("getCurrentChallangePlayerRecord");
        $this->setPublicMethod("getRecords");
    }

    public function exp_onLoad() {
        parent::exp_onLoad();
        $this->enableStorageEvents();
        $this->enableDedicatedEvents();
<<<<<<< HEAD
        $this->enableDatabase();
    }
=======
        $this->enablePluginEvents();
        $this->setPublicMethod("getRecords");
        $this->registerChatCommand("top100", "showRanks", 0, true);

        $this->registerChatCommand("save", "saveRecords", 0, true, \ManiaLive\Features\Admin\AdminGroup::get());
        $this->registerChatCommand("load", "loadRecords", 0, true, \ManiaLive\Features\Admin\AdminGroup::get());
        $this->registerChatCommand("reset", "resetRecords", 0, true, \ManiaLive\Features\Admin\AdminGroup::get());

        if (!$this->db->tableExists("exp_players")) {
            $this->db->execute('CREATE TABLE IF NOT EXISTS `exp_players` (
  `login` varchar(255) NOT NULL,
  `nickname` text NOT NULL,
  `nation` text,
  `language` text,
  PRIMARY KEY (`login`),
  UNIQUE KEY `login` (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
        }
>>>>>>> 5627efa21b36b83cf932a38364c1cbd5da4f212e

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
        $this->buildCurrentChallangeRecords();
        $this->updateCurrentChallengeRecords();
    }

    public function onBeginMap($map, $warmUp, $matchContinuation) {
        $this->updateCurrentChallengeRecords();
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

<<<<<<< HEAD
=======
        foreach ($this->storage->players as $player)
            $this->onPlayerConnect($player->login, false);
        foreach ($this->storage->spectators as $player)
            $this->onPlayerConnect($player->login, true);
>>>>>>> 5627efa21b36b83cf932a38364c1cbd5da4f212e
    }
    
    public function onPlayerConnect($login, $isSpectator) {
        $uid = $this->storage->currentMap->uId;
        $this->getFromDbPlayerRecord($login, $uid);
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
            if ($nrecord->place == $recordrank_old && !$force) {
                $this->exp_chatSendServerMessage($this->msg_secure, null, array($nrecord->nickName, $nrecord->place, \ManiaLive\Utilities\Time::fromTM($nrecord->time), $recordrank_old, $recordtime_old));
            } else {
                $this->exp_chatSendServerMessage($this->msg_new, null, array($nrecord->nickName, $nrecord->place, \ManiaLive\Utilities\Time::fromTM($nrecord->time), $recordrank_old, $recordtime_old));
            }
            \ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_UPDATE_RECORDS, $this->currentChallengeRecords));
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
            $q = 'UPDATE exp_`exp_records`
                        SET	`record_score` = ' . $this->db->quote($record->time) . ',
                            `record_nbFinish` = ' . $this->db->quote($record->nbFinish) . ',
                            `record_avgScore` = ' . $this->db->quote($record->avgScore) . ', 
                            `record_checkpoints` = ' . $this->db->quote(implode(",", $record->ScoreCheckpoints)) . ', 
                            `record_date` = ' . $this->db->quote($record->date) . '
                        WHERE `record_challengeuid` = ' . $this->db->quote($uid) . '
                            AND `record_playerlogin` =  ' . $this->db->quote($record->login) . '
                            ADN `record_nbLaps` = ' . $this->db->quote($nbLaps) . ';';

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
            $this->getFromDbPlayerRecord($login,$uid);
        }

        foreach ($this->storage->spectators as $login => $player) { // get spectators
            $this->getFromDbPlayerRecord($login,$uid);
        }
        \ManiaLive\Event\Dispatcher::dispatch(new Event(Event::ON_UPDATE_RECORDS, $this->currentChallengeRecords));
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
            $record->ScoreCheckpoints = explode(",", $data->record_checkpoints);

            $records[$i] = $record;
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
        
        if(isset($this->currentChallengePlayerRecords[$login]))
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
            $record->ScoreCheckpoints = explode(",", $data->record_checkpoints);

           $this->currentChallengePlayerRecords[$login] = $record;
        } else {
            return false;
        }
    }
    
    public function getCurrentChallangePlayerRecord($login){
        return isset($this->currentChallengePlayerRecords[$login]) ? $this->currentChallengePlayerRecords[$login] : null;
    }
    
    public function getRecords(){
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

<<<<<<< HEAD
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
=======
    function onPlayerDisconnect($login) {
        
>>>>>>> 5627efa21b36b83cf932a38364c1cbd5da4f212e
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
