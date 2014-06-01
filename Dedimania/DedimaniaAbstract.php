<?php

namespace ManiaLivePlugins\eXpansion\Dedimania;

use ManiaLivePlugins\eXpansion\Dedimania\Classes\Connection as DediConnection;
use ManiaLivePlugins\eXpansion\Dedimania\Events\Event as DediEvent;
use ManiaLive\DedicatedApi\Callback\Event as Event;
use ManiaLivePlugins\eXpansion\Dedimania\Structures\DediPlayer;
use ManiaLivePlugins\eXpansion\Dedimania\Structures\DediRecord;
use \ManiaLive\Event\Dispatcher;
use \ManiaLive\Utilities\Console;
use ManiaLivePlugins\eXpansion\Dedimania\Config;

/**
 * Description of DedimaniaAbstract
 *
 * @author De Cramer Oliver
 */
abstract class DedimaniaAbstract extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin implements \ManiaLivePlugins\eXpansion\Dedimania\Events\Listener {

    /** @var DediConnection */
    protected $dedimania;
    protected $running = false;

    /** @var Config */
    protected $config;

    /** @var Structures\DediRecord[] $records */
    protected $records = array();

    /** @var array */
    protected $rankings = array();

    /** @var string */
    protected $vReplay = "";

    /** @var string */
    protected $gReplay = "";

    /** @var Structures\DediRecord */
    protected $lastRecord;

    /* @var integer $recordCount */
    protected $recordCount = 30;

    /* @var bool $warmup */
    protected $wasWarmup = false;
    protected $msg_newRecord, $msg_norecord, $msg_record;

    public function exp_onInit() {
	$this->setPublicMethod("isRunning");
	$this->config = Config::getInstance();
    }

    public function exp_onLoad() {
	$helpText = "\n\nPlease correct your config with these instructions: \nEdit and add following configuration lines to manialive config.ini\n\n ManiaLivePlugins\\eXpansion\\Dedimania_Script\\Config.login = 'your_server_login_here' \n ManiaLivePlugins\\eXpansion\\Dedimania_Script\\Config.code = 'your_server_code_here' \n\n Visit http://dedimania.net/tm2stats/?do=register to get code for your server.";
	if (empty($this->config->login)) {
	    $this->console("Server login is not configured for dedimania plugin!");
	    $this->running = false;
	}
	if (empty($this->config->code)) {
	    $this->console("Server code is not configured for dedimania plugin!");
	    $this->running = false;
	}
	Dispatcher::register(DediEvent::getClass(), $this);
	$this->dedimania = DediConnection::getInstance();
	$this->msg_record = exp_getMessage('%1$s#dedirecord# claimed the #rank#%2$s#dedirecord#. Dedimania Record!  #rank#%2$s: #time#%3$s #dedirecord#(#rank#%4$s #time#-%5$s#dedirecord#)');
	$this->msg_newRecord = exp_getMessage('%1$s#dedirecord# claimed the #rank#%2$s#dedirecord#. Dedimania Record! #time#%3$s');
	$this->msg_norecord = exp_getMessage('#dedirecord#No dedimania records found for the map!');
    }

    public function exp_onReady() {
	parent::exp_onReady();
	$this->enableDedicatedEvents();
	$this->enableApplicationEvents();
	\ManiaLive\Event\Dispatcher::register(\ManiaLivePlugins\eXpansion\Core\Events\ScriptmodeEvent::getClass(), $this);

	//$this->registerChatCommand("test", "test",0,true);
	$this->tryConnection();
    }

    private $settingsChanged = array();

    public function onSettingsChanged(\ManiaLivePlugins\eXpansion\Core\types\config\Variable $var) {
	$this->settingsChanged[$var->getName()] = true;
	if ($this->settingsChanged['login'] && $this->settingsChanged['code']) {
	    $this->tryConnection();
	    $this->settingsChanged = array();
	}
    }

    function tryConnection() {
	if (!$this->running) {
	    if (empty($this->config->login) || empty($this->config->code)) {
		$admins = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance();
		$admins->announceToPermission('expansion_settings', "#admin_error#Server login or/and Server code is empty in Dedimania Configuration");
		$this->console("Server code or/and login is not configured for dedimania plugin!");
	    } else {
		try {
		    $this->dedimania->openSession($this->expStorage->version->titleId, $this->config);
		    $this->registerChatCommand("dedirecs", "showRecs", 0, true);
		    $this->running = true;
		    $admins = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance();
		    $admins->announceToPermission('expansion_settings', "#admin_action#Dedimania connection successfull.");
		} catch (\Exception $ex) {
		    $admins = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance();
		    $admins->announceToPermission('expansion_settings', "#admin_error#Server login or/and Server code is wrong in Dedimania Configuration");
		    $admins->announceToPermission('expansion_settings', "#admin_error#" . $ex->getMessage());
		    $this->console("Server code or/and login is wrong for the dedimania plugin!");
		}
	    }
	}
    }

    function checkSession($login) {
	$this->dedimania->checkSession();
    }

    public function test($login) {
	echo " login\n";
	$ranks = $this->connection->getCurrentRankingForLogin($login);
	print_r($ranks);

	echo " all\n";
	$ranks = $this->connection->getCurrentRanking(-1, 0);
	print_r($ranks);


	print_r($this->connection->getServerPackMask());
    }

    public function onPlayerConnect($login, $isSpectator) {
	if (!$this->running)
	    return;
	$player = $this->storage->getPlayerObject($login);
	$this->dedimania->playerConnect($player, $isSpectator);
    }

    public function onPlayerDisconnect($login, $reason = null) {
	if (!$this->running)
	    return;
	$this->dedimania->playerDisconnect($login);
    }

    public function onBeginMatch() {
	if (!$this->running)
	    return;
	// echo "beginMatch\n";
	$this->records = array();
	$this->dedimania->getChallengeRecords();
    }

    public function onBeginRound() {
	if (!$this->running)
	    return;
	// echo "beginround\n";
	$this->wasWarmup = $this->connection->getWarmUp();
    }

    /**
     * rearrages the records list + recreates the indecies
     * @param string $login is passed to check the server,map and player maxranks for new driven record
     */
    function reArrage($login) {
// sort by time
	$this->sortAsc($this->records, "time");

	$i = 0;
	$newrecords = array();
	foreach ($this->records as $record) {
	    $i++;
	    if (array_key_exists($record->login, $newrecords))
		continue;

	    $record->place = $i;
// if record holder is at server, then we must check for additional   
	    if ($record->login == $login) {
		echo "login: $login\n";
// if record is greater than players max rank, don't allow
		if ($record->place > DediConnection::$players[$login]->maxRank) {
		    echo "record place: " . $record->place . " is greater than player max rank: " . DediConnection::$players[$login]->maxRank;
		    echo "\n not adding record.\n";
		    continue;
		}
// if player record is greater what server can offer, don't allow
		if ($record->place > DediConnection::$serverMaxRank) {
		    echo "record place: " . $record->place . " is greater than server max rank: " . DediConnection::$serverMaxRank;
		    echo "\n not adding record.\n";
		    continue;
		}
// if player record is greater than what is allowed, don't allow
		if ($record->place > DediConnection::$dediMap->mapMaxRank) {
		    echo "record place: " . $record->place . " is greater than record max count: " . DediConnection::$dediMap->mapMaxRank;
		    echo "\n not adding record.\n";
		    continue;
		}

		echo "\n DEDIRECORD ADDED.\n";
// update checkpoints for the record

		$playerinfo = \ManiaLivePlugins\eXpansion\Core\Core::$playerInfo;

		$record->checkpoints = implode(",", $playerinfo[$login]->checkpoints);

// add record
		$newrecords[$record->login] = $record;
// othervice
	    } else {
// check if some record needs to be erased from the list...
//  if ($record->place > DediConnection::$dediMap->mapMaxRank)
//  continue;

		$newrecords[$record->login] = $record;
	    }
	}

// assign  the new records
//$this->records = array_slice($newrecords, 0, DediConnection::$dediMap->mapMaxRank);

	$this->records = $newrecords;
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

    public function compare_bestTime($a, $b) {
	if ($a['BestTime'] == $b['BestTime'])
	    return 0;
	return ($a['BestTime'] < $b['BestTime']) ? -1 : 1;
    }

    private function sortAsc(&$array, $prop) {
	usort($array, function($a, $b) use ($prop) {
	    return $a->$prop > $b->$prop ? 1 : -1;
	});
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
	    $this->records[$record['Login']] = new DediRecord($record['Login'], $record['NickName'], $record['MaxRank'], $record['Best'], $record['Rank'], $record['Checks']);
	}
	$this->lastRecord = end($this->records);
	$this->recordCount = count($this->records);

	$this->debug("Dedimania get records:");
    }

    public function exp_onUnload() {
	$this->disableTickerEvent();
	$this->disableDedicatedEvents();
	\ManiaLivePlugins\eXpansion\Dedimania\Gui\Windows\Records::EraseAll();
    }

    /**
     * 
     * @param type $data
     */
    public function onDedimaniaUpdateRecords($data) {
	$this->debug("Dedimania update records:");
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

	    $this->exp_chatSendServerMessage($this->msg_newRecord, $recepient, array(\ManiaLib\Utils\Formatting::stripCodes($record->nickname, "wos"), $record->place, $time));
	} catch (\Exception $e) {
	    $this->console("Error: couldn't show dedimania message" . $e->getMessage());
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

	    $this->exp_chatSendServerMessage($this->msg_record, $recepient, array(\ManiaLib\Utils\Formatting::stripCodes($record->nickname, "wos"), $record->place, $time, $oldRecord->place, $diff));
	    $this->debug("message sent.");
	} catch (\Exception $e) {
	    $this->console("Error: couldn't show dedimania message");
	}
    }

    public function onDedimaniaPlayerConnect($data) {
	
    }

    public function onDedimaniaPlayerDisconnect($login) {
	
    }

    public function showRecs($login) {
	\ManiaLivePlugins\eXpansion\Dedimania\Gui\Windows\Records::Erase($login);

	if (sizeof($this->records) == 0) {
	    $this->exp_chatSendServerMessage($this->msg_norecord, $login);
	    return;
	}
	try {
	    $window = \ManiaLivePlugins\eXpansion\Dedimania\Gui\Windows\Records::Create($login);
	    $window->setTitle(__('Dedimania -records on a Map', $login));
	    $window->centerOnScreen();
	    $window->populateList($this->records);
	    $window->setSize(120, 100);
	    $window->show();
	} catch (\Exception $e) {
	    echo $e->getFile() . ":" . $e->getLine();
	}
    }

    public function isRunning() {
	return $this->running;
    }

}

?>
