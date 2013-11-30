<?php

namespace ManiaLivePlugins\eXpansion\Dedimania;

use ManiaLivePlugins\eXpansion\Dedimania\Classes\Connection as DediConnection;
use ManiaLivePlugins\eXpansion\Dedimania\Events\Event as DediEvent;
use ManiaLivePlugins\eXpansion\Dedimania\Config;
use \ManiaLive\Event\Dispatcher;
use \ManiaLive\Utilities\Console;

class Dedimania extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin implements \ManiaLivePlugins\eXpansion\Dedimania\Events\Listener {

    /** @var DediConnection */
    private $dedimania;

    /** @var Config */
    private $config;

    /** @var Structures\DediRecord[$login] $records */
    private $records = array();

    /** @var array */
    private $rankings = array();

    /** @var string */
    private $vReplay = "";

    /** @var string */
    private $gReplay = "";

    /** @var Structures\DediRecord */
    private $lastRecord;

    /* @var integer $recordCount */
    private $recordCount = 30;

    public function exp_onInit() {
	$this->setVersion(0.1);
	$this->config = Config::getInstance();
    }

    public function exp_onLoad() {
	if ($this->isPluginLoaded("Reaby\\Dedimania") || $this->isPluginLoaded("Flo\\Dedimania"))
	    die("[eXpansion] Please disable other dedimania plugins, you don't need multiple ones!");
	$helpText = "\n please correct your config with these instructions: \n add following configuration to config.ini\n\n ManiaLivePlugins\\eXpansion\\Dedimania\\Config.login = 'your_server_login_here' \n ManiaLivePlugins\\eXpansion\\Dedimania\\Config.code = 'your_server_code_here' \n visit http://dedimania.net/tm2stats/?do=register to get code for your server.\n\n";
	if (empty($this->config->login))
	    die("[Dedimania] Server login is not configured!" . $helpText);
	if (empty($this->config->code))
	    die("[Dedimania] Server code is not configured!" . $helpText);
	Dispatcher::register(DediEvent::getClass(), $this);
	$this->dedimania = DediConnection::getInstance();
	$this->config->newRecordMsg = exp_getMessage($this->config->newRecordMsg);
	$this->config->noRecordMsg = exp_getMessage($this->config->noRecordMsg);
	$this->config->recordMsg = exp_getMessage($this->config->recordMsg);
    }

    public function exp_onReady() {
	$this->enableDedicatedEvents();
	$this->enableApplicationEvents();
	$this->registerChatCommand("dedirecs", "showRecs", 0, true);
	$this->dedimania->openSession();
    }

    function checkSession($login) {
	$this->dedimania->checkSession();
    }

    public function onPlayerConnect($login, $isSpectator) {
	$player = $this->storage->getPlayerObject($login);
	$this->dedimania->playerConnect($player, $isSpectator);
    }

    public function onPlayerDisconnect($login, $reason = null) {
	$this->dedimania->playerDisconnect($login);
    }

    public function onBeginMatch() {
	$this->records = array();
	$this->dedimania->getChallengeRecords();
    }

    public function onBeginMap($map, $warmUp, $matchContinuation) {
	$this->records = array();
	$this->rankings = array();
	$this->vReplay = "";
	$this->gReplay = "";
    }

    public function onPlayerFinish($playerUid, $login, $time) {
	if ($time == 0)
	    return;

	if ($this->storage->currentMap->nbCheckpoints == 1)
	    return;

	if (!array_key_exists($login, DediConnection::$players))
	    return;

	// if player is banned from dedimania, don't send his time.
	if (DediConnection::$players[$login]->banned)
	    return;

	// if current map doesn't have records, create one.
	if (count($this->records) == 0) {
	    $player = $this->connection->getCurrentRankingForLogin($login);
	    // map first array entry to player object;
	    $player = $player[0];
	    $this->records[$login] = new Structures\DediRecord($login, $player->nickName, DediConnection::$players[$login]->maxRank, $time, -1, $player->bestCheckpoints);
	    $this->reArrage();
	    \ManiaLive\Event\Dispatcher::dispatch(new DediEvent(DediEvent::ON_NEW_DEDI_RECORD, $this->records[$login]));
	    return;
	}

	// if last record is not set, don't continue.
	if (!is_object($this->lastRecord)) {
	    return;
	}

	$maxrank = DediConnection::$serverMaxRank;
	if (DediConnection::$players[$login]->maxRank > $maxrank) {
	    $maxrank = DediConnection::$players[$login]->maxRank;
	}

	// so if the time is better than the last entry or the count of records is less than $maxrank
	print "$time <= " . $this->lastRecord->time . " || " . count($this->records) . " <= $maxrank \n";
	var_dump(($time <= $this->lastRecord->time || count($this->records) <= $maxrank) ? true : false);

	if ($time <= $this->lastRecord->time || count($this->records) <= $maxrank) {
	    print "times matches!";

	    // if player exists on the list... see if he got better time
	    if (array_key_exists($login, $this->records)) {
		if ($this->records[$login]->time > $time) {
		    $oldRecord = $this->records[$login];
		    $player = $this->connection->getCurrentRankingForLogin($login);
		    // map first array entry to player object;
		    $player = $player[0];
		    // get records count
		    $oldCount = count($this->records);
		    $this->records[$login] = new Structures\DediRecord($login, $player->nickName, DediConnection::$players[$login]->maxRank, $time, -1, $player->bestCheckpoints);
		    // if new records count is greater than old count, increase the map records limit
		    print count($this->records) . " > " . $oldCount . " && " . (DediConnection::$dediMap->mapMaxRank + 1 ) . " < " . DediConnection::$players[$login]->maxRank . "\n";
		    var_dump((count($this->records) > $oldCount) && ( (DediConnection::$dediMap->mapMaxRank + 1 ) < DediConnection::$players[$login]->maxRank) ? true : false);

		    if ((count($this->records) > $oldCount) && ( (DediConnection::$dediMap->mapMaxRank + 1 ) < DediConnection::$players[$login]->maxRank)) {
			print "increasing maxrank! \n";
			DediConnection::$dediMap->mapMaxRank++;
		    }
		    $this->reArrage();
		    // have to recheck if the player is still at the dedi array
		    if (array_key_exists($login, $this->records)) // have to recheck if the player is still at the dedi array
			\ManiaLive\Event\Dispatcher::dispatch(new DediEvent(DediEvent::ON_DEDI_RECORD, $this->records[$login], $oldRecord));
		    return;
		}

		// if not, add the player to records table
	    } else {
		print "got deditime!";
		$player = $this->connection->getCurrentRankingForLogin($login);
		// map first array entry to player object;
		$player = $player[0];
		$oldCount = count($this->records);
		$this->records[$login] = new Structures\DediRecord($login, $player->nickName, DediConnection::$players[$login]->maxRank, $time, -1, $player->bestCheckpoints);
		// if new records count is greater than old count, increase the map records limit
		print count($this->records) . " > " . $oldCount . " && " . (DediConnection::$dediMap->mapMaxRank + 1 ) . " < " . DediConnection::$players[$login]->maxRank . "\n";
		var_dump((count($this->records) > $oldCount) && ( (DediConnection::$dediMap->mapMaxRank + 1 ) < DediConnection::$players[$login]->maxRank) ? true : false);

		if ((count($this->records) > $oldCount) && ( (DediConnection::$dediMap->mapMaxRank + 1 ) < DediConnection::$players[$login]->maxRank)) {
		    print "increasing maxrank! \n";
		    DediConnection::$dediMap->mapMaxRank++;
		}
		$this->reArrage();

		// have to recheck if the player is still at the dedi array
		if (array_key_exists($login, $this->records))
		    \ManiaLive\Event\Dispatcher::dispatch(new DediEvent(DediEvent::ON_NEW_DEDI_RECORD, $this->records[$login]));
		return;
	    }
	}
    }

    function reArrage() {
	// sort by time
	$this->sortAsc($this->records, "time");

	$i = 1;
	$newrecords = array();
	foreach ($this->records as $record) {
	    if (array_key_exists($record->login, $newrecords))
		continue;

	    $record->place = $i;
	    // if player is at server
	    // if ($record->place <= DediConnection::$dediMap->mapMaxRank) {
	    $newrecords[$record->login] = $record;
	    // }
	    $i++;
	}
	// assign  the new records
	$this->records = array_slice($newrecords, 0, DediConnection::$dediMap->mapMaxRank);
	//$this->records = $newrecords;
	// assign the last place
	$this->lastRecord = end($this->records);

	// recreate new records entry for update_records
	$data = array('Records' => array());
	$i = 1;
	foreach ($this->records as $record) {
	    $data['Records'][] = Array("Login" => $record->login, "MaxRank" => $record->maxRank, "NickName" => $record->nickname, "Best" => $record->time, "Rank" => $i, "Checks" => $record->checkpoints);
	    $i++;
	}

	\ManiaLive\Event\Dispatcher::dispatch(new DediEvent(DediEvent::ON_UPDATE_RECORDS, $data));
    }

    private function sortAsc(&$array, $prop) {
	usort($array, function($a, $b) use ($prop) {
		    return $a->$prop > $b->$prop ? 1 : -1;
		});
    }

    /**
     * 
     * @param type $rankings
     * @param type $map
     * @param type $wasWarmUp
     * @param type $matchContinuesOnNextMap
     * @param type $restartMap
     */
    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {
	// $this->dedimania->updateServerPlayers($this->storage->currentMap); // optional
    }

    /**
     * 
     * @param array $rankings
     * @param string $winnerTeamOrMap
     * 
     */
    public function onEndMatch($rankings, $winnerTeamOrMap) {
	$this->rankings = $rankings;

	try {
	    if (sizeof($rankings) == 0) {
		$this->vReplay = "";
		$this->gReplay = "";
		return;
	    }
	    $this->vReplay = $this->connection->getValidationReplay($rankings[0]['Login']);
	    $greplay = "";
	    $grfile = sprintf('Dedimania/%s.%d.%07d.%s.Replay.Gbx', $this->storage->currentMap->uId, $this->storage->gameInfos->gameMode, $rankings[0]['BestTime'], $rankings[0]['Login']);
	    $this->connection->SaveBestGhostsReplay($rankings[0]['Login'], $grfile);
	    $this->gReplay = file_get_contents($this->connection->gameDataDirectory() . 'Replays/' . $grfile);

	    // Dedimania doesn't allow times sent without validation relay. So, let's just stop here if there is none.
	    if (empty($this->vReplay)) {
		Console::println("[Dedimania] Couldn't get validation replay of the first player. Dedimania times not sent.");
		return;
	    }

	    $this->dedimania->setChallengeTimes($this->storage->currentMap, $this->rankings, $this->vReplay, $this->gReplay);
	} catch (\Exception $e) {
	    Console::println("[Dedimania] " . $e->getMessage());
	    $this->vReplay = "";
	    $this->gReplay = "";
	}
    }

    public function onDedimaniaOpenSession() {
	$players = array();
	foreach ($this->storage->players as $player) {
	    if ($player->login != $this->storage->serverLogin)
		$players[] = array($player, false);
	}
	foreach ($this->storage->spectators as $player)
	    $players[] = array($player, true);

	$this->dedimania->playerMultiConnect($players);

	$this->dedimania->getChallengeRecords();
	$this->rankings = array();
    }

    public function onDedimaniaGetRecords($data) {
	$this->records = array();

	foreach ($data['Records'] as $record) {
	    $this->records[$record['Login']] = new Structures\DediRecord($record['Login'], $record['NickName'], $record['MaxRank'], $record['Best'], $record['Rank'], $record['Checks']);
	}
	$this->lastRecord = end($this->records);
	$this->recordCount = count($this->records);

	$this->debug("Dedimania get records:");
	//$this->debug($data);
    }

    public function onUnload() {
	$this->disableTickerEvent();
	$this->disableDedicatedEvents();
	parent::onUnload();
    }

    /**
     * 
     * @param type $data
     */
    public function onDedimaniaUpdateRecords($data) {
	//$this->debug("Dedimania update records:");
	// $this->debug($data);
    }

    /**
     * onDedimaniaNewRecord($record)
     * gets called on when player has driven a new record for the map
     * 
     * @param Structures\DediRecord $record     
     */
    public function onDedimaniaNewRecord($record) {
	try {
	    if ($this->config->disableMessages == true)
		return;

	    $recepient = $record->login;
	    if ($this->config->show_record_msg_to_all)
		$recepient = null;

	    $time = \ManiaLive\Utilities\Time::fromTM($record->time);
	    if (substr($time, 0, 3) === "0:0") {
		$time = substr($time, 3);
	    } else if (substr($time, 0, 2) === "0:") {
		$time = substr($time, 2);
	    }

	    $this->exp_chatSendServerMessage($this->config->newRecordMsg, $recepient, array(\ManiaLib\Utils\Formatting::stripCodes($record->nickname, "wos"), $record->place, $time));
	} catch (\Exception $e) {
	    \ManiaLive\Utilities\Console::println("Error: couldn't show dedimania message" . $e->getMessage());
	}
    }

    /**
     * 
     * @param Structures\DediRecord $record
     * @param Structures\DediRecord $oldRecord     
     */
    public function onDedimaniaRecord($record, $oldRecord) {
	$this->debug("improved dedirecord:");
	$this->debug($record);
	try {
	    if ($this->config->disableMessages == true)
		return;
	    $recepient = $record->login;
	    if ($this->config->show_record_msg_to_all)
		$recepient = null;

	    $diff = \ManiaLive\Utilities\Time::fromTM($record->time - $oldRecord->time);
	    if (substr($diff, 0, 3) === "0:0") {
		$diff = substr($diff, 3);
	    } else if (substr($diff, 0, 2) === "0:") {
		$diff = substr($diff, 2);
	    }
	    $time = \ManiaLive\Utilities\Time::fromTM($record->time);
	    if (substr($time, 0, 3) === "0:0") {
		$time = substr($time, 3);
	    } else if (substr($time, 0, 2) === "0:") {
		$time = substr($time, 2);
	    }

	    $this->exp_chatSendServerMessage($this->config->recordMsg, $recepient, array(\ManiaLib\Utils\Formatting::stripCodes($record->nickname, "wos"), $record->place, $time, $oldRecord->place, $diff));
	    $this->debug("message sent.");
	} catch (\Exception $e) {
	    \ManiaLive\Utilities\Console::println("Error: couldn't show dedimania message");
	}
    }

    public function onDedimaniaPlayerConnect($data) {
	
    }

    public function onDedimaniaPlayerDisconnect($login) {
	
    }

    public function showRecs($login) {
	Gui\Windows\Records::Erase($login);

	if (sizeof($this->records) == 0) {
	    $this->exp_chatSendServerMessage($this->config->noRecordMsg, $login);
	    return;
	}
	try {
	    $window = Gui\Windows\Records::Create($login);
	    $window->setTitle(__('Dedimania -records on a Map', $login));
	    $window->centerOnScreen();
	    $window->populateList($this->records);
	    $window->setSize(120, 100);
	    $window->show();
	} catch (\Exception $e) {
	    echo $e->getFile() . ":" . $e->getLine();
	}
    }

}

?>
