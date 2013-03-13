<?php

namespace ManiaLivePlugins\eXpansion\Database;

/**
 * Description of Database
 *
 * @author oliverde8
 */
class Database  extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

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
    }

    public function initCreateTables() {
        if (!$this->db->tableExists('exp_databaseversion'))
            $this->createDatabaseTable();

        if (!$this->db->tableExists('exp_players'))
            $this->createPlayersTable();
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
                            " . $this->db->quote($player->nickName) . ",
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
                `player_nicknameStripped` = " . $this->db->quote($player->nickName) . ",
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
        }

        foreach ($this->storage->spectators as $login => $player) { // get spectators
            $this->updatePlayTime($player);
        }
    }

    function updatePlayTime($player) {

        $time = time();

        if (isset($player->lastTimeUpdate)) {
            $playtime = $time - $player->lastTimeUpdate;
            $q = "UPDATE `exp_players`
			 SET player_timeplayed` = (`player_timeplayed` + $playtime)
			 WHERE `player_login` = " . $this->db->quote($player->login) . ";";
            $this->db->query($q);
        }

        $player->lastTimeUpdate = $time;
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

            $q = "UPDATE exp_`databaseversion`
			SET	`database_version` = " . $this->db->quote($version) . "
			WHERE `database_table` = " . $this->db->quote($table) . ";";

            $this->db->query($q);
        }
    }

}

?>
