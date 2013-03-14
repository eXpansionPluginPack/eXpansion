<?php

namespace ManiaLivePlugins\eXpansion\Dedimania;

use ManiaLivePlugins\eXpansion\Dedimania\Classes\Connection as DediConnection;
use ManiaLivePlugins\eXpansion\Dedimania\Structures\Request;
use \ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\Dedimania\Events\Event as DediEvent;
use ManiaLivePlugins\eXpansion\Dedimania\Config;

class Dedimania extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin implements \ManiaLivePlugins\eXpansion\Dedimania\Events\Listener {

    /** @var DediConnection */
    public $dedimania;

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
        Dispatcher::register(DediEvent::getClass(), $this);
        $this->config = Config::getInstance();
        \ManiaLivePlugins\eXpansion\Core\ColorParser::getInstance()->registerCode("dedirecord", $this->config->color_dedirecord);
        \ManiaLivePlugins\eXpansion\Core\ColorParser::getInstance()->registerCode("dedirecord_variable", $this->config->color_dedirecord_variable);
    }

    public function exp_onLoad() {
        $this->enableDedicatedEvents();
        $this->enableApplicationEvents();
        $this->dedimania = DediConnection::getInstance();
    }

    public function exp_onReady() {
//  $this->registerChatCommand("check", "checkSession", 0, true);
        $this->dedimania->openSession();
    }

    function checkSession($login) {
        $this->dedimania->checkSession();
    }

    public function onPlayerConnect($login, $isSpectator) {
        $player = $this->storage->getPlayerObject($login);
        $this->dedimania->playerConnect($player, $isSpectator);
    }

    public function onPlayerDisconnect($login) {
        $this->dedimania->playerDisconnect($login);
    }

    public function onBeginMap($map, $warmUp, $matchContinuation) {
        $this->records = array();
        $this->dedimania->getChallengeRecords();
        $this->rankings = array();
        $this->vReplay = "";
        $this->gReplay = "";
    }

    public function onPlayerFinish($playerUid, $login, $time) {
        if ($time == 0)
            return;

        if (!array_key_exists($login, DediConnection::$players))
            return;


        if (DediConnection::$players[$login]->banned)
            return;

        $player = $this->storage->getPlayerObject($login);
        if (count($this->records) == 0) {
            $this->records[$login] = new Structures\DediRecord($login, $player->nickName, $time);
            \ManiaLive\Event\Dispatcher::dispatch(new DediEvent(DediEvent::ON_NEW_DEDI_RECORD, $this->records[$login]));
            $this->reArrage();
            $this->announce($login);
        }

        if (!is_object($this->lastRecord)) {
            echo "lastRecord not set";
            return;
        }

        // so if the time is better than the last entry or the count of records is less than 20...
        if ($this->lastRecord->time > $time || count($this->records) < DediConnection::$serverMaxRank) {
            // if player exists on the list... see if he got better time
            if (array_key_exists($login, $this->records)) {
                if ($this->records[$login]->time > $time) {
                    $oldRecord = $this->records[$login];
                    $this->records[$login] = new Structures\DediRecord($login, $player->nickName, $time);
                    \ManiaLive\Event\Dispatcher::dispatch(new DediEvent(DediEvent::ON_NEW_DEDI_RECORD, $this->records[$login]));
                    $this->reArrage();
                    $this->announce($login, $oldRecord);

                    return;
                }
                // if not then just do a update for the time
            } else {
                $this->records[$login] = new Structures\DediRecord($login, $player->nickName, $time);
                \ManiaLive\Event\Dispatcher::dispatch(new DediEvent(DediEvent::ON_NEW_DEDI_RECORD, $this->records[$login]));
                $this->reArrage();
                $this->announce($login);
                return;
            }
        }
    }

    function reArrage() {
        \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::sortAsc($this->records, "time");
        $i = 0;
        $newrecords = array();
        foreach ($this->records as $record) {
            if (array_key_exists($record->login, $newrecords))
                continue;
            $record->place = ++$i;
            if (array_key_exists($record->login, DediConnection::$players)) {
                if ($record->place < DediConnection::$players[$record->login]->maxRank) {
                    echo "record added";
                    $newrecords[$record->login] = $record;
                }
            } else {
                $newrecords[$record->login] = $record;
            }
        }

        $this->records = array_slice($newrecords, 0, DediConnection::$serverMaxRank);
        $this->lastRecord = end($this->records);

        $data = array('Records' => array());
        foreach ($this->records as $record) {
            $data['Records'][] = Array("NickName" => $record->nickname, "Best" => $record->time);
        }

        \ManiaLive\Event\Dispatcher::dispatch(new DediEvent(DediEvent::ON_UPDATE_DEDI_RECORDS, $data));
    }

    function announce($login, $oldRecord = null) {
        try {
            if (!array_key_exists($login, $this->records))
                return;

            $player = $this->storage->getPlayerObject($login);

            $suffix = "th";
            $grats = __("#dedirecord#new dedimania record: ");
            switch ($this->records[$login]->place) {
                case 1:
                    $suffix = "st";
                    break;
                case 2:
                    $suffix = "nd";
                    break;
                case 3:
                    $suffix = "rd";

                    break;
            }

            if ($oldRecord !== null) {
                $diff = \ManiaLive\Utilities\Time::fromTM($this->records[$login]->time - $oldRecord->time, true);
                $this->exp_chatSendServerMessage($grats . '#dedirecord_variable#$o %s$o%s #dedirecord#for#dedirecord_variable# %s $z$s#dedirecord#with a time of$o#dedirecord_variable# %s $o#dedirecord#$n(%s)', null, array($this->records[$login]->place, $suffix, \ManiaLib\Utils\Formatting::stripCodes($player->nickName, "wos"), \ManiaLive\Utilities\Time::fromTM($this->records[$login]->time), $diff));
                return;
            }

            $this->exp_chatSendServerMessage($grats . '#dedirecord_variable#$o %s$o%s #dedirecord#for#dedirecord_variable# %s $z$s#dedirecord#with a time of$o#dedirecord_variable# %s', null, array($this->records[$login]->place, $suffix, \ManiaLib\Utils\Formatting::stripCodes($player->nickName, "wos"), \ManiaLive\Utilities\Time::fromTM($this->records[$login]->time)));
        } catch (\Exception $e) {
            \ManiaLive\Utilities\Console::println("Error: couldn't show dedimania message" . $e->getMessage());
        }
    }

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {
        $this->dedimania->setChallengeTimes($map, $this->rankings, $this->vReplay, $this->gReplay);
        $this->dedimania->updateServerPlayers($map);
    }

    public function onEndMatch($rankings, $winnerTeamOrMap) {
        $this->rankings = $rankings;

        try {
            $this->vReplay = $this->connection->getValidationReplay($rankings[0]['Login']);
            $greplay = "";
            $grfile = sprintf('Dedimania/%s.%d.%07d.%s.Replay.Gbx', $this->storage->currentMap->uId, $this->storage->gameInfos->gameMode, $rankings[0]['BestTime'], $rankings[0]['Login']);
            $this->connection->SaveBestGhostsReplay($rankings[0]['Login'], $grfile);
            $this->gReplay = file_get_contents($this->connection->gameDataDirectory() . 'Replays/' . $grfile);
        } catch (\Exception $e) {
            echo "\n\n\nERROR:" . $e->getMessage() . "\n\n\n";
            $this->vReplay = "";
            $this->gReplay = "";
        }
    }

    public function onDedimaniaOpenSession($data) {
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
        print_r(array_keys($data));

        $this->records = array();
        $this->recordCount = $data['ServerMaxRank'];

        foreach ($data['Records'] as $record) {
            $this->records[$record['Login']] = new Structures\DediRecord($record['Login'], $record['NickName'], $record['Best'], $record['Rank']);
        }
        $this->lastRecord = end($this->records);
    }

    public function onUnload() {
        $this->disableTickerEvent();
        $this->disableDedicatedEvents();
        parent::onUnload();
    }

    public function onDedimaniaNewRecord($data) {
        
    }

    public function onDedimaniaUpdateRecords($data) {
        
    }

}

?>
