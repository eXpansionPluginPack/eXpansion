<?php

namespace ManiaLivePlugins\eXpansion\Dedimania;

use ManiaLive\Application\ErrorHandling;
use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Dedimania\Classes\Connection as DediConnection;
use ManiaLivePlugins\eXpansion\Dedimania\Events\Event as DediEvent;
use ManiaLivePlugins\eXpansion\Dedimania\Structures\DediRecord;

/**
 * Description of DedimaniaAbstract
 *
 * @author De Cramer Oliver
 */
abstract class DedimaniaAbstract extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin implements \ManiaLivePlugins\eXpansion\Dedimania\Events\Listener
{
    const DEBUG_NONE = 0; //00000
    const DEBUG_MAX_RANKS = 1; //00001

    public $debug = self::DEBUG_MAX_RANKS;

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
    public static $actionOpenRecs = -1;
    public static $actionOpenCps = -1;

    public function expOnInit()
    {
        $this->setPublicMethod("isRunning");
        $this->config = Config::getInstance();
    }

    public function eXpOnLoad()
    {
        $helpText = "\n\nPlease correct your config with these instructions: \nEdit and add following configuration "
            ."lines to manialive config.ini\n\n ManiaLivePlugins\\eXpansion\\Dedimania_Script\\Config."
            ."login = 'your_server_login_here' \n "
            ."ManiaLivePlugins\\eXpansion\\Dedimania_Script\\Config.code = 'your_server_code_here' \n\n "
            ."Visit http://dedimania.net/tm2stats/?do=register to get code for your server.";
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
        $this->msg_record = eXpGetMessage(
            '%1$s#dedirecord# claimed the #rank#%2$s#dedirecord#. '
            .'Dedimania Record!  #rank#%2$s: #time#%3$s #dedirecord#(#rank#%4$s #time#-%5$s#dedirecord#)'
        );
        $this->msg_newRecord = eXpGetMessage(
            '%1$s#dedirecord# claimed the #rank#%2$s#dedirecord#. Dedimania Record! #time#%3$s'
        );
        $this->msg_norecord = eXpGetMessage('#dedirecord#No dedimania records found for the map!');
    }

    public function eXpOnReady()
    {
        parent::eXpOnReady();
        $this->enableDedicatedEvents();
        $this->enableApplicationEvents();
        $this->enableStorageEvents();

        \ManiaLive\Event\Dispatcher::register(
            \ManiaLivePlugins\eXpansion\Core\Events\ScriptmodeEvent::getClass(),
            $this
        );

        $this->tryConnection();
    }

    private $settingsChanged = array();

    public function onSettingsChanged(\ManiaLivePlugins\eXpansion\Core\types\config\Variable $var)
    {
        $this->settingsChanged[$var->getName()] = true;
        if ($this->settingsChanged['login'] && $this->settingsChanged['code']) {
            $this->tryConnection();
            $this->settingsChanged = array();
        }
    }

    public function tryConnection()
    {
        if (!$this->running) {
            if (empty($this->config->login) || empty($this->config->code)) {
                $admins = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance();
                $admins->announceToPermission(
                    Permission::EXPANSION_PLUGIN_SETTINGS,
                    "#admin_error#Server login or/and Server code is empty in Dedimania Configuration"
                );
                $this->console("\$f00Server code or/and login is not configured for dedimania plugin!");
            } else {
                try {
                    $this->dedimania->openSession($this->expStorage->version->titleId, $this->config);
                    $this->registerChatCommand("dedirecs", "showRecs", 0, true);
                    $this->registerChatCommand("dedicps", "showCps", 0, true);
                    $this->setPublicMethod("showRecs");
                    $this->setPublicMethod("showCps");

                    $this->running = true;
                    $admins = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance();
                    $admins->announceToPermission(
                        'expansion_settings',
                        "#admin_action#Dedimania connection successfull."
                    );

                    self::$actionOpenRecs = \ManiaLive\Gui\ActionHandler::getInstance()
                        ->createAction(array($this, "showRecs"));
                    self::$actionOpenCps = \ManiaLive\Gui\ActionHandler::getInstance()
                        ->createAction(array($this, "showCps"));
                } catch (\Exception $ex) {
                    $admins = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance();
                    $admins->announceToPermission(
                        'expansion_settings',
                        "#admin_error#Server login or/and Server code is wrong in Dedimania Configuration"
                    );
                    $admins->announceToPermission('expansion_settings', "#admin_error#" . $ex->getMessage());
                    $this->console("\$f00Server code or/and login is wrong for the dedimania plugin!");
                }
            }
        }
    }

    public function checkSession($login)
    {
        $this->dedimania->checkSession();
    }

    public function onPlayerConnect($login, $isSpectator)
    {
        if (!$this->running) {
            return;
        }
        $player = $this->storage->getPlayerObject($login);
        $this->dedimania->playerConnect($player, $isSpectator);
    }

    public function onPlayerDisconnect($login, $reason = null)
    {
        if (!$this->running) {
            return;
        }
        $this->dedimania->playerDisconnect($login);
    }

    public function onBeginMatch()
    {
        if (!$this->running) {
            return;
        }
        $this->records = array();
        $this->dedimania->getChallengeRecords();
    }

    public function onBeginRound()
    {
        if (!$this->running) {
            return;
        }
        $this->wasWarmup = $this->connection->getWarmUp();
    }

    /**
     * rearrages the records list + recreates the indecies
     *
     * @param string $login is passed to check the server,map and player maxranks for new driven record
     */
    public function reArrage($login)
    {
        // sort by time
        $this->sortAsc($this->records, "time");

        $maxrank = DediConnection::$serverMaxRank;
        if (DediConnection::$players[$login]->maxRank > $maxrank) {
            $maxrank = DediConnection::$players[$login]->maxRank;
        }

        $this->debugMaxRanks('Server Max Rank is : ' . DediConnection::$serverMaxRank);
        $this->debugMaxRanks('Checking with      : ' . $maxrank);

        $i = 0;
        $newrecords = array();
        foreach ($this->records as $record) {
            $i++;
            if (array_key_exists($record->login, $newrecords)) {
                continue;
            }

            $record->place = $i;
            // if record holder is at server, then we must check for additional
            if ($record->login == $login) {
                // if record is greater than players max rank, don't allow
                if ($record->place > $maxrank) {
                    $this->debugMaxRanks("record place: " . $record->place . " is greater than max rank: " . $maxrank);
                    $this->debugMaxRanks("not adding record.");
                    continue;
                }


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
            $data['Records'][] = array(
                "Login" => $record->login, "MaxRank" => $record->maxRank, "NickName" => $record->nickname,
                "Best" => $record->time, "Rank" => $i, "Checks" => $record->checkpoints
            );
            $i++;
        }

        \ManiaLive\Event\Dispatcher::dispatch(new DediEvent(DediEvent::ON_UPDATE_RECORDS, $data));
    }

    public function compare_bestTime($a, $b)
    {
        if ($a['BestTime'] == $b['BestTime']) {
            return 0;
        }

        return ($a['BestTime'] < $b['BestTime']) ? -1 : 1;
    }

    private function sortAsc(&$array, $prop)
    {
        usort($array, function ($a, $b) use ($prop) {
            return $a->$prop > $b->$prop ? 1 : -1;
        });
    }

    public function onDedimaniaOpenSession()
    {
        $players = array();
        foreach ($this->storage->players as $player) {
            if ($player->login != $this->storage->serverLogin) {
                $players[] = array($player, false);
            }
        }
        foreach ($this->storage->spectators as $player) {
            $players[] = array($player, true);
        }

        $this->dedimania->playerMultiConnect($players);

        $this->dedimania->getChallengeRecords();

        $this->rankings = array();
    }

    public function onDedimaniaGetRecords($data)
    {

        $this->records = array();

        foreach ($data['Records'] as $record) {
            $this->records[$record['Login']] = new DediRecord(
                $record['Login'],
                $record['NickName'],
                $record['MaxRank'],
                $record['Best'],
                $record['Rank'],
                $record['Checks']
            );
        }
        $this->lastRecord = end($this->records);
        $this->recordCount = count($this->records);

        $this->debug("Dedimania get records:");
    }

    public function eXpOnUnload()
    {
        $this->disableTickerEvent();
        $this->disableDedicatedEvents();
        \ManiaLivePlugins\eXpansion\Dedimania\Gui\Windows\Records::EraseAll();
        \ManiaLive\Gui\ActionHandler::getInstance()->deleteAction(self::$actionOpenCps);
        \ManiaLive\Gui\ActionHandler::getInstance()->deleteAction(self::$actionOpenRecs);
        self::$actionOpenRecs = -1;
        self::$actionOpenCps = -1;


        Dispatcher::unregister(DediEvent::getClass(), $this);
    }

    /**
     *
     * @param type $data
     */
    public function onDedimaniaUpdateRecords($data)
    {
        $this->debug("Dedimania update records:");
    }

    /**
     * onDedimaniaNewRecord($record)
     * gets called on when player has driven a new record for the map
     *
     * @param Structures\DediRecord $record
     */
    public function onDedimaniaNewRecord($record)
    {
        try {
            if ($this->config->disableMessages == true) {
                return;
            }

            $recepient = $record->login;
            if ($this->config->show_record_msg_to_all) {
                $recepient = null;
            }

            $time = \ManiaLive\Utilities\Time::fromTM($record->time);
            if (substr($time, 0, 3) === "0:0") {
                $time = substr($time, 3);
            } else {
                if (substr($time, 0, 2) === "0:") {
                    $time = substr($time, 2);
                }
            }

            $this->eXpChatSendServerMessage(
                $this->msg_newRecord,
                $recepient,
                array(\ManiaLib\Utils\Formatting::stripCodes($record->nickname, "wos"), $record->place, $time)
            );
        } catch (\Exception $e) {
            $this->console("Error: couldn't show dedimania message" . $e->getMessage());
        }
    }

    /**
     *
     * @param Structures\DediRecord $record
     * @param Structures\DediRecord $oldRecord
     */
    public function onDedimaniaRecord($record, $oldRecord)
    {
        $this->debug("improved dedirecord:");
        $this->debug($record);
        try {
            if ($this->config->disableMessages == true) {
                return;
            }
            $recepient = $record->login;
            if ($this->config->show_record_msg_to_all) {
                $recepient = null;
            }

            $diff = \ManiaLive\Utilities\Time::fromTM($record->time - $oldRecord->time);
            if (substr($diff, 0, 3) === "0:0") {
                $diff = substr($diff, 3);
            } else {
                if (substr($diff, 0, 2) === "0:") {
                    $diff = substr($diff, 2);
                }
            }
            $time = \ManiaLive\Utilities\Time::fromTM($record->time);
            if (substr($time, 0, 3) === "0:0") {
                $time = substr($time, 3);
            } else {
                if (substr($time, 0, 2) === "0:") {
                    $time = substr($time, 2);
                }
            }

            $this->eXpChatSendServerMessage(
                $this->msg_record,
                $recepient,
                array(
                    \ManiaLib\Utils\Formatting::stripCodes($record->nickname, "wos"),
                    $record->place,
                    $time,
                    $oldRecord->place, $diff
                )
            );
            $this->debug("message sent.");
        } catch (\Exception $e) {
            $this->console("Error: couldn't show dedimania message");
        }
    }

    public function onDedimaniaPlayerConnect($data)
    {

    }

    public function onDedimaniaPlayerDisconnect($login)
    {

    }

    public function showRecs($login)
    {
        \ManiaLivePlugins\eXpansion\Dedimania\Gui\Windows\Records::Erase($login);

        if (sizeof($this->records) == 0) {
            $this->eXpChatSendServerMessage($this->msg_norecord, $login);

            return;
        }
        try {
            $window = \ManiaLivePlugins\eXpansion\Dedimania\Gui\Windows\Records::Create($login);
            $window->setTitle(__('Dedimania -records for', $login), $this->storage->currentMap->name);
            $window->centerOnScreen();
            $window->populateList($this->records);
            $url = "http://dedimania.net/tm2stats/?do=stat&Envir=" . $this->storage->currentMap->environnement
                . "&RecOrder3=REC-ASC&UId=" . $this->storage->currentMap->uId . "&Show=RECORDS";
            $window->setDediUrl($url);

            $window->setSize(120, 100);
            $window->show();
        } catch (\Exception $e) {
            ErrorHandling::displayAndLogError($e);
        }
    }

    public function showCps($login)
    {
        \ManiaLivePlugins\eXpansion\Dedimania\Gui\Windows\RecordCps::Erase($login);

        if (sizeof($this->records) == 0) {
            $this->eXpChatSendServerMessage($this->msg_norecord, $login);

            return;
        }
        try {
            $window = \ManiaLivePlugins\eXpansion\Dedimania\Gui\Windows\RecordCps::Create($login);
            $window->setTitle(__('Dedimania cps for ', $login), $this->storage->currentMap->name);
            $window->centerOnScreen();
            $window->populateList($this->records);

            $window->setSize(170, 110);
            $window->show();
        } catch (\Exception $e) {
            ErrorHandling::displayAndLogError($e);
        }
    }

    public function isRunning()
    {
        return $this->running;
    }

    protected function debugMaxRanks($debugMsg)
    {
        if (($this->debug & self::DEBUG_MAX_RANKS) == self::DEBUG_MAX_RANKS) {
            $this->console('[Max Ranks]' . $debugMsg);
        }
    }
}
