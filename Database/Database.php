<?php

namespace ManiaLivePlugins\eXpansion\Database;

use ManiaLive\Utilities\Console;
use ManiaLib\Utils\Formatting as String;
use \ManiaLivePlugins\eXpansion\Database\Config;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
/**
 * Description of Database
 *
 * @author oliverde8
 */
class Database extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $config;

    function exp_onInit() {
	$this->config = Config::getInstance();
    }

    public function exp_onLoad() {
	parent::exp_onLoad();
	try {
	    $this->enableDatabase();
	} catch (\Exception $e) {
	    $this->dumpException("There seems be a problem while establishing a MySQL connection.", $e);
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
	$this->setPublicMethod('showDbMaintainance');
	$this->updateServerChallenges();
	// add admin command ;)
	$cmd = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::addAdminCommand('dbtools', $this, 'showDbMaintainance', Permission::server_database); //
	$cmd->setHelp('shows administrative window for database');
	$cmd->setMinParam(0);
    }

    public function exp_onReady() {
	
    }

    public function onPlayerConnect($login, $isSpec) {
	$g = "SELECT * FROM `exp_players` WHERE `player_login` = " . $this->db->quote($login) . ";";
	$query = $this->db->execute($g);
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
	    $this->db->execute($q);
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
	    $this->db->execute($q);
	}
    }

    function onPlayerDisconnect($login, $reason = null) {
	$this->updatePlayTime($this->storage->getPlayerObject($login));
    }

    function onEndMatch($rankings, $winnerTeamOrMap) {
	foreach ($this->storage->players as $login => $player) { // get players
	     $this->updatePlayTime($player);
	    if ($player->rank == 1 && sizeof($this->storage->players) > 1)
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
	if (empty($player) || (!$player->spectator && $this->expStorage->isRelay))
	    return;

	if (isset($player->lastTimeUpdate)) {
	    $playtime = $time - $player->lastTimeUpdate;
	    $q = "UPDATE `exp_players`
             SET `player_timeplayed` = (`player_timeplayed` + $playtime)
             WHERE `player_login` = " . $this->db->quote($player->login) . ";";
	    $this->db->execute($q);
	}
	$player->lastTimeUpdate = $time;
    }

    function updateServerChallenges() {
	
	//get database challenges
	$uids = "";
	$mapsByUid = array();
	foreach ($this->storage->maps as $map) {
	    $uids .= $this->db->quote($map->uId) . ",";
	    $mapsByUid[$map->uId] = $map;
	}
	$uids = trim($uids, ",");
	$g = "SELECT * FROM `exp_maps`  WHERE challenge_uid IN ($uids);";
	$query = $this->db->execute($g);
	
	
	while ($data = $query->fetchStdObject()) {
	    $mapsByUid[$data->challenge_uid]->addTime = $data->challenge_addtime;
	    unset($mapsByUid[$data->challenge_uid]);
	}

	if(!empty($mapsByUid)){
	    foreach($mapsByUid as $map){
		$this->insertMap($map);
		$map->addTime = time();
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
	$this->db->execute($q);
    }

    public function initCreateTables() {
	if (!$this->db->tableExists('exp_databaseversion'))
	    $this->createDatabaseTable();

	if (!$this->db->tableExists('exp_players'))
	    $this->createPlayersTable();

	if ($this->getDatabaseVersion('exp_players') == 1) {
	    $this->updatePlayersTableTo2();
	}

	if (!$this->db->tableExists('exp_maps'))
	    $this->createMapTable();
	if ($this->getDatabaseVersion('exp_maps') != 2) {
	    $this->db->execute('ALTER TABLE exp_maps ADD KEY(challenge_uid);');
	    $this->setDatabaseVersion('exp_maps', 2);
	}
    }

    public function createDatabaseTable() {

	$q = "CREATE TABLE `exp_databaseversion` (
                    `database_id` mediumint(9) NOT NULL AUTO_INCREMENT,
                    `database_table` varchar(50) NOT NULL,
                    `database_version` mediumint(9) NOT NULL,
                     PRIMARY KEY (`database_id`),
                     KEY(`database_table`)
                ) CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM;";
	$this->db->execute($q);
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
	$this->db->execute($q);
    }

    public function updatePlayersTableTo2() {
	$q = "ALTER TABLE `exp_players` CHANGE `player_timeplayed` `player_timeplayed` INT( 12 ) NOT NULL DEFAULT '0';";
	$this->db->execute($q);
	$q = "ALTER TABLE `exp_players` CHANGE `player_updated` `player_updated` INT( 12 ) NOT NULL DEFAULT '0';";
	$this->db->execute($q);
	$this->setDatabaseVersion('exp_players', 2);
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
	$this->db->execute($q);
    }

    public function getPlayer($login) {
	$g = "SELECT * FROM `exp_players` WHERE `player_login` = " . $this->db->quote($login) . ";";

	$query = $this->db->execute($g);

	if ($query->recordCount() == 0) {
	    return null;
	} else {
	    $player = $query->fetchStdObject();
	    return $player;
	}
    }

    public function incrementWins($player) {
	$q = "UPDATE `exp_players`
             SET `player_wins` = (`player_wins` + 1)
             WHERE `player_login` = " . $this->db->quote($player->login) . ";";
	$this->db->execute($q);
	if ($this->config->showWins) {
	    $q = "SELECT `player_wins` FROM `exp_players` WHERE `player_login` = " . $this->db->quote($player->login) . ";";
	    $query = $this->db->execute($q);
	    $data = $query->fetchStdObject();
	    $w = $data->player_wins;
	    $msg_pub = exp_getMessage('#rank#Congratulations to #variable#%1$s#rank# for their #variable#%2$s#rank# win!');
	    $msg_self = exp_getMessage('#rank#Congratulations for your #variable#%1$s#rank# win!');
	    $wins = $this->numberize($w);
	    if ($w <= 100 && $w % 10 == 0) {
		$this->exp_chatSendServerMessage($msg_pub, null, array(\ManiaLib\Utils\Formatting::stripCodes($player->nickName, "wosnm"), $wins));
	    } else if ($w % 25 == 0) {
		$this->exp_chatSendServerMessage($msg_pub, null, array(\ManiaLib\Utils\Formatting::stripCodes($player->nickName, "wosnm"), $wins));
	    } else {
		$this->exp_chatSendServerMessage($msg_self, $player->login, array($wins));
	    }
	}
    }

    function numberize($num) {
	if ($num >= 10 && $num <= 20) {
	    $num = $num . 'th';
	} else if (substr($num, -1) == 1) {
	    $num = $num . 'st';
	} else if (substr($num, -1) == 2) {
	    $num = $num . 'nd';
	} else if (substr($num, -1) == 3) {
	    $num = $num . 'rd';
	} else {
	    $num = $num . 'th';
	}
	return $num;
    }

    function getDatabaseVersion($table, $fromPlugin = null) {
	$g = "SELECT * FROM `exp_databaseversion` WHERE `database_table` = " . $this->db->quote($table) . ";";
	$query = $this->db->execute($g);

	if ($query->recordCount() == 0) {
	    return false;
	} else {
	    $record = $query->fetchStdObject();
	    return $record->database_version;
	}
    }

    function showDbMaintainance($login) {
	if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, Permission::server_database)) {
	    $window = Gui\Windows\Maintainance::Create($login);
	    $window->init($this->db);
	    $window->setTitle(__('Database Maintainance'));
	    $window->centerOnScreen();
	    $window->setSize(160, 100);

	    $window->show();
	}
    }

    function showBackupRestore($login) {
	if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, Permission::server_database)) {
	    $window = Gui\Windows\BackupRestore::Create($login);
	    $window->init($this->db);
	    $window->setTitle(__('Database Backup and Restore'));
	    $window->centerOnScreen();
	    $window->setSize(160, 100);
	    $window->show();
	}
    }

    function setDatabaseVersion($table, $version) {

	$g = "SELECT * FROM `exp_databaseversion` WHERE `database_table` = " . $this->db->quote($table) . ";";
	$query = $this->db->execute($g);

	if ($query->recordCount() == 0) {

	    $q = "INSERT INTO `exp_databaseversion` (`database_table`,
                                 `database_version`
                                 ) VALUES (
                                 " . $this->db->quote($table) . ",
                                 " . $this->db->quote($version) . "
                                 )";
	    $this->db->execute($q);
	} else {
	    $record = $query->fetchStdObject();

	    if ($record->database_version < $version) {
		$q = "UPDATE `exp_databaseversion`
                SET `database_version` = " . $this->db->quote($version) . "
                WHERE `database_table` = " . $this->db->quote($table) . ";";
		echo $q;
		$this->db->execute($q);
	    }
	}
    }

}

?>
