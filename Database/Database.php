<?php

namespace ManiaLivePlugins\eXpansion\Database;

use ManiaLib\Utils\Formatting as String;

/**
 * Description of Database
 *
 * @author oliverde8
 */
class Database extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    public function exp_onLoad() {
        parent::exp_onLoad();
        try {
            $this->enableDatabase();
        } catch (\Exception $e) {
            Console::printLn('');
            Console::printLn('Oops, there seems be a problem while establishing a MySQL connection.');
            Console::printLn('');
            Console::printLn('MySQL said:');
            Console::printLn($e->getMessage());
            Console::printLn('');
            die();
        }
        $this->enableDedicatedEvents();
        $this->initCreateTables();

        foreach ($this->storage->players as $login => $player) { // get players
            $this->onPlayerConnect($login, false);
        }
        foreach ($this->storage->spectators as $login => $player) { // get spectators
            $this->onPlayerConnect($login, false);
        }
        
        $this->setPublicMethod('getPlayer');
        $this->setPublicMethod('getDatabaseVersion');
        $this->setPublicMethod('setDatabaseVersion');
        $this->updateServerChallenges();
    }

    public function onPlayerConnect($login, $isSpec) {
        $g = "SELECT * FROM `exp_players` WHERE `player_login` = " . $this->db->quote($login) . ";";
        $query = $this->db->query($g);
        // get player data
        $time = \time();
        $player = $this->storage->getPlayerObject($login);
        $this->storage->getPlayerObject($login)->lastTimeUpdate = $time;

        if ($query->recordCount() == 0) {
            $q = "INSERT INTO `exp_players` 
                    (`player_login`,`player_nickname`, `player_nicknameStripped`, `player_updated`, `player_ip`,
                        `player_onlinerights`, `player_nation`, `player_wins`, `player_timeplayed`)
                    VALUES (" . $this->db->quote($player->login) . ",
                            " . $this->db->quote($player->nickName) . ",
                            " . $this->db->quote(String::stripColors($player->nickName)) . ",
                            " . $this->db->quote($time) . ",
                            " . $this->db->quote($player->iPAddress) . ",
                            " . $this->db->quote($player->onlineRights) . ",
                            " . $this->db->quote($player->path) . ",
                            0,
                            0
                            )";
            $this->db->query($q);
        } else {
            $q = "UPDATE `exp_players`
			 SET
                `player_nickname` = " . $this->db->quote($player->nickName) . ",
                `player_nicknameStripped` = " . $this->db->quote(String::stripColors($player->nickName)) . ",
                `player_updated` = " . $this->db->quote($time) . ",
                `player_ip` =  " . $this->db->quote($player->iPAddress) . ",
                `player_onlinerights` = " . $this->db->quote($player->onlineRights) . "
			 WHERE
			 `player_login` = " . $this->db->quote($login) . ";";
            $this->db->query($q);
        }
    }

    function onPlayerDisconnect($login) {
        $this->updatePlayTime($this->storage->getPlayerObject($login));
    }

    function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {
        foreach ($this->storage->players as $login => $player) { // get players
            $this->updatePlayTime($player);
            if ($player->rank == 1)
                $this->incrementWins($player);
        }

        foreach ($this->storage->spectators as $login => $player) { // get spectators
            $this->updatePlayTime($player);
        }
    }
    
     public function onMapListModified($curMapIndex, $nextMapIndex, $isListModified) {
         $this->updateServerChallenges();
     }

    function updatePlayTime($player) {
        $time = time();

        if (isset($player->lastTimeUpdate)) {
            $playtime = $time - $player->lastTimeUpdate;
            $q = "UPDATE `exp_players`
			 SET `player_timeplayed` = (`player_timeplayed` + $playtime)
			 WHERE `player_login` = " . $this->db->quote($player->login) . ";";
            $this->db->query($q);
        }

        $player->lastTimeUpdate = $time;
    }
    
    function updateServerChallenges() {
        //get server challenges
        $serverChallenges = $this->storage->maps;
        //get database challenges

        $g = "SELECT * FROM `exp_maps`;";
        $query = $this->db->query($g);

        $databaseUid = array();
        //get database uid's of tracks.
        while ($data = $query->fetchStdObject()) {
            $databaseUid[$data->challenge_uid] = $data->challenge_uid;
        }

        unset($data);
        $addCounter = 0;
        foreach ($serverChallenges as $data) {
            // check if database doesn't have the challenge already.
            if (!array_key_exists($data->uId, $databaseUid)) {
                $this->insertMap($data);
                $addCounter++;
            }
        }
    }
    
     public function insertMap($data, $login = 'n/a') {
        if (empty($data->mood)) {
            $connection = $this->connection;
            try {
                $data = $connection->getMapInfo($data->fileName);
            } catch (\Exception $e) {
                //$this->sendChat('%adminerror%' . $e->getMessage(), $login);
            }
        }

        $q = "INSERT INTO `exp_maps` (`challenge_uid`,
                                    `challenge_name`,
                                    `challenge_nameStripped`,
                                    `challenge_file`,
                                    `challenge_author`,
                                    `challenge_environment`,
                                    `challenge_mood`,
                                    `challenge_bronzeTime`,
                                    `challenge_silverTime`,
                                    `challenge_goldTime`,
                                    `challenge_authorTime`,
                                    `challenge_copperPrice`,
                                    `challenge_lapRace`,
                                    `challenge_nbLaps`,
                                    `challenge_nbCheckpoints`,
                                    `challenge_addedby`,
                                    `challenge_addtime`
                                    )
                                VALUES (" . $this->db->quote($data->uId) . ",
                                " . $this->db->quote($data->name) . ",
                                " . $this->db->quote(String::stripColors($data->name)) . ",
                                " . $this->db->quote($data->fileName) . ",
                                " . $this->db->quote($data->author) . ",
                                " . $this->db->quote($data->environnement) . ",
                                " . $this->db->quote($data->mood) . ",
                                " . $this->db->quote($data->bronzeTime) . ",
                                " . $this->db->quote($data->silverTime) . ",
                                " . $this->db->quote($data->goldTime) . ",
                                " . $this->db->quote($data->authorTime) . ",
                                " . $this->db->quote($data->copperPrice) . ",
                                " . $this->db->quote($data->lapRace) . ",
                                " . $this->db->quote($data->nbLaps) . ",
                                " . $this->db->quote($data->nbCheckpoints) . ",
                                " . $this->db->quote($login) . ",
                                " . $this->db->quote(time()) . "
                                )";
        $this->db->query($q);
    }

    public function initCreateTables() {
        if (!$this->db->tableExists('exp_databaseversion'))
            $this->createDatabaseTable();

        if (!$this->db->tableExists('exp_players'))
            $this->createPlayersTable();

        if (!$this->db->tableExists('exp_maps'))
            $this->createMapTable();
    }

    public function createDatabaseTable() {

        $q = "CREATE TABLE `exp_databaseversion` (
					`database_id` mediumint(9) NOT NULL AUTO_INCREMENT,
					`database_table` varchar(50) NOT NULL,
					`database_version` mediumint(9) NOT NULL,
					 PRIMARY KEY (`database_id`),
                     KEY(`database_table`)
                ) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM;";
        $this->db->query($q);
    }

    function createPlayersTable() {
        if ($this->getDatabaseVersion('exp_players') == false) {
            $this->setDatabaseVersion('exp_players', 1);
        }
        $q = "CREATE TABLE `exp_players` (
					`player_login` varchar(50) NOT NULL,
					`player_nickname` varchar(100) NOT NULL,
					`player_nicknameStripped` varchar(100) NOT NULL,
					`player_updated` mediumint(9) NOT NULL DEFAULT '0',
					`player_wins` mediumint(9) NOT NULL DEFAULT '0',
					`player_timeplayed` mediumint(9) NOT NULL DEFAULT '0',
					`player_onlinerights` varchar(10) NOT NULL,
					`player_ip` varchar(50),
					`player_nation` varchar(100),
					PRIMARY KEY (`player_login`)
                ) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM;";
        $this->db->query($q);
    }

    public function createMapTable() {
        if ($this->getDatabaseVersion('exp_maps') == false) {
            $this->setDatabaseVersion('exp_maps', 1);
        }

        $q = "CREATE TABLE `exp_maps` (
                                    `challenge_id` MEDIUMINT( 5 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                                    `challenge_uid` VARCHAR( 27 ) NOT NULL ,
                                    `challenge_name` VARCHAR( 100 ) NOT NULL ,
                                    `challenge_nameStripped` VARCHAR( 100 ) NOT NULL ,
                                    `challenge_file` VARCHAR( 200 ) NOT NULL ,
                                    `challenge_author` VARCHAR( 30 ) NOT NULL ,
                                    `challenge_environment` VARCHAR( 15 ) NOT NULL,

                                    `challenge_mood` VARCHAR( 50 ) NOT NULL,
                                    `challenge_bronzeTime` INT( 10 ) NOT NULL,
                                    `challenge_silverTime` INT( 10 ) NOT NULL,
                                    `challenge_goldTime` INT( 10 ) NOT NULL,
                                    `challenge_authorTime` INT( 10 ) NOT NULL,
                                    `challenge_copperPrice` INT( 10 ) NOT NULL,
                                    `challenge_lapRace` INT( 3 ) NOT NULL,
                                    `challenge_nbLaps` INT( 3 ) NOT NULL,
                                    `challenge_nbCheckpoints` INTEGER( 3 ) NOT NULL,
                                    `challenge_addedby` VARCHAR(200),
                                    `challenge_addtime` INT(9)
                                    ) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = MYISAM ;";
        $this->db->query($q);
    }
    
    public function getPlayer($login){
        $g = "SELECT * FROM `exp_players` WHERE `player_login` = " . $this->db->quote($login) . ";";

        $query = $this->db->query($g);

        if ($query->recordCount() == 0) {
            return null;
        } else {
            $player = $query->fetchStdObject();
            return $player;
        }
    }

    public function incrementWins($player) {
        $q = "UPDATE `exp_players`
			 SET player_wins` = (`player_wins` + 1)
			 WHERE `player_login` = " . $this->db->quote($player->login) . ";";
        $this->db->query($q);
    }

    function getDatabaseVersion($table, $fromPlugin = null) {
        $g = "SELECT * FROM `exp_databaseversion` WHERE `database_table` = " . $this->db->quote($table) . ";";
        $query = $this->db->query($g);

        if ($query->recordCount() == 0) {
            return false;
        } else {
            $record = $query->fetchStdObject();
            return $record->database_version;
        }
    }

    function setDatabaseVersion($table, $version) {

        $g = "SELECT * FROM `exp_databaseversion` WHERE `database_table` = " . $this->db->quote($table) . ";";
        $query = $this->db->query($g);

        if ($query->recordCount() == 0) {

            $q = "INSERT INTO `exp_databaseversion` (`database_table`,
								 `database_version`
								 ) VALUES (
								 " . $this->db->quote($table) . ",
								 " . $this->db->quote($version) . "
								 )";
            $this->db->query($q);
        } else {
            $record = $query->fetchStdObject();
            
            if($record->database_version < $version){            
                $q = "UPDATE exp_`databaseversion`
                SET	`database_version` = " . $this->db->quote($version) . "
                WHERE `database_table` = " . $this->db->quote($table) . ";";

                $this->db->query($q);
            }
        }
    }

}

?>
