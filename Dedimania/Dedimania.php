<?php

namespace ManiaLivePlugins\eXpansion\Dedimania;

use ManiaLivePlugins\eXpansion\Dedimania\Classes\Connection as DediConnection;
use ManiaLivePlugins\eXpansion\Dedimania\Events\Event as DediEvent;
use ManiaLivePlugins\eXpansion\Dedimania\Config;
use \ManiaLive\Event\Dispatcher;
use \ManiaLive\Utilities\Console;

class Dedimania extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin implements \ManiaLivePlugins\eXpansion\Dedimania\Events\Listener {

    /** @var \ManiaLivePlugins\eXpansion\Dedimania\Classes\Connection */
    private $dedimania;

    /** @var Config */
    private $config;
    private $records = array();
    private $rankings = array();
    private $vReplay = "";
    private $gReplay = "";
    private $lastRecord;
    private $recordCount = 15;

    public function exp_onInit() {
        $this->setVersion(0.1);
        $this->config = Config::getInstance();
        Dispatcher::register(DediEvent::getClass(), $this);
    }

    public function exp_onLoad() {
        if ($this->isPluginLoaded("Reaby\\Dedimania") || $this->isPluginLoaded("Flo\\Dedimania"))
            die("[eXpansion] Please disable other dedimania plugins, you don't need multiple ones!");
        $helpText = "\n please correct your config with these instructions: \n add following configuration to config.ini\n\n ManiaLivePlugins\\eXpansion\\Dedimania\\Config.login = 'your_server_login_here' \n ManiaLivePlugins\\eXpansion\\Dedimania\\Config.code = 'your_server_code_here' \n\n";
        if (empty($this->config->login))
            die("[Dedimania] Server login is not configured!" . $helpText);
        if (empty($this->config->code))
            die("[Dedimania] Server code is not configured!" . $helpText);

        $this->dedimania = DediConnection::getInstance();
        $this->config->newRecordMsg = exp_getMessage($this->config->newRecordMsg);
        $this->config->noRecordMsg = exp_getMessage($this->config->noRecordMsg);
        $this->config->recordMsg = exp_getMessage($this->config->recordMsg);

        \ManiaLivePlugins\eXpansion\Core\ColorParser::getInstance()->registerCode("dedirecord", $this->config->color_dedirecord);
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
        $this->rankings = array();
        $this->vReplay = "";
        $this->gReplay = "";
    }

    public function onBeginMap($map, $warmUp, $matchContinuation) {
        
    }

    public function onPlayerFinish($playerUid, $login, $time) {
        if ($time == 0)
            return;

        if ($this->storage->currentMap->nbCheckpoints == 1)
            return;

        if (!array_key_exists($login, DediConnection::$players))
            return;


        if (DediConnection::$players[$login]->banned)
            return;

        $player = $this->storage->getPlayerObject($login);
        if (count($this->records) == 0) {
            $this->records[$login] = new Structures\DediRecord($login, $player->nickName, $time);
            $this->reArrage();
            \ManiaLive\Event\Dispatcher::dispatch(new DediEvent(DediEvent::ON_NEW_DEDI_RECORD, $this->records[$login]));
        }

        if (!is_object($this->lastRecord)) {

            return;
        }

        // so if the time is better than the last entry or the count of records is less than 20...
        if ($this->lastRecord->time > $time || count($this->records) < DediConnection::$serverMaxRank) {
            // if player exists on the list... see if he got better time
            if (array_key_exists($login, $this->records)) {
                if ($this->records[$login]->time > $time) {
                    $oldRecord = $this->records[$login];
                    $this->records[$login] = new Structures\DediRecord($login, $player->nickName, $time);
                    $this->reArrage();
                    if (array_key_exists($login, $this->records)) // have to recheck if the player is still at the dedi array
                        \ManiaLive\Event\Dispatcher::dispatch(new DediEvent(DediEvent::ON_DEDI_RECORD, $this->records[$login], $oldRecord));
                    return;
                }
                // if not then just do a update for the time
            } else {
                $this->records[$login] = new Structures\DediRecord($login, $player->nickName, $time);
                $this->reArrage();
                if (array_key_exists($login, $this->records)) // have to recheck if the player is still at the dedi array
                    \ManiaLive\Event\Dispatcher::dispatch(new DediEvent(DediEvent::ON_NEW_DEDI_RECORD, $this->records[$login]));
                return;
            }
        }
    }

    function reArrage() {
        $this->sortAsc($this->records, "time");

        $i = 0;
        $newrecords = array();
        foreach ($this->records as $record) {
            if (array_key_exists($record->login, $newrecords))
                continue;
            $record->place = ++$i;
            if (array_key_exists($record->login, DediConnection::$players)) {
                if ($record->place < DediConnection::$players[$record->login]->maxRank) {
                    $newrecords[$record->login] = $record;
                }
            } else {
                $newrecords[$record->login] = $record;
            }
        }
        // assign  the new records
        $this->records = array_slice($newrecords, 0, DediConnection::$serverMaxRank);
        // assign the last place
        $this->lastRecord = end($this->records);

        // recreate new records entry for update_records
        $data = array('Records' => array());
        foreach ($this->records as $record) {
            $data['Records'][] = Array("Login" => $record->login, "NickName" => $record->nickname, "Best" => $record->time, "Checks" => $record->checkpoints);
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
        // Dedimania doesn't allow times sent without valiadition relay. So, let's just stop here if there is none.
        if (empty($this->vReplay)) {
            Console::println("[Dedimania Notice] Couldn't get validation replay of the first player. Dedimania times not sent.");
            return;
        }
        
        $map = $this->connection->getCurrentMapInfo();
        $this->dedimania->setChallengeTimes($map, $this->rankings, $this->vReplay, $this->gReplay);
        $this->dedimania->updateServerPlayers($this->storage->currentMap);
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
        } catch (\Exception $e) {
            Console::println("[Dedimania] Exception: " . $e->getMessage());
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
        $this->recordCount = $data['ServerMaxRank'];

        foreach ($data['Records'] as $record) {
            $this->records[$record['Login']] = new Structures\DediRecord($record['Login'], $record['NickName'], $record['Best'], $record['Rank'], $record['Checks']);
        }
        $this->lastRecord = end($this->records);
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

            $this->exp_chatSendServerMessage($this->config->newRecordMsg, $recepient, array(\ManiaLib\Utils\Formatting::stripCodes($record->nickname, "wos"), $record->place, \ManiaLive\Utilities\Time::fromTM($record->time)));
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
        try {
            if ($this->config->disableMessages == true)
                return;
            $recepient = $record->login;
            if ($this->config->show_record_msg_to_all)
                $recepient = null;

            $diff = \ManiaLive\Utilities\Time::fromTM($record->time - $oldRecord->time, true);
            $this->exp_chatSendServerMessage($this->config->recordMsg, $recepient, array(\ManiaLib\Utils\Formatting::stripCodes($record->nickname, "wos"), $record->place, \ManiaLive\Utilities\Time::fromTM($record->time), $oldRecord->place, $diff));
        } catch (\Exception $e) {
            \ManiaLive\Utilities\Console::println("Error: couldn't show dedimania message");
            print_r($e);
        }
    }

    public function onDedimaniaPlayerConnect($data) {
        if ($this->config->disableMessages)
            return;

        if ($data == null)
            return;

        if ($data['Banned']) {
            return;
        }

        $player = $this->storage->getPlayerObject($data['Login']);
        $type = '$fffFree';

        if ($data['MaxRank'] > 15) {
            $type = '$ff0Premium$fff';
            $upgrade = false;
        }
    }

    public function onDedimaniaPlayerDisconnect() {
        
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
