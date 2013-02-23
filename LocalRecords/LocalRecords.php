<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords;

use \ManiaLivePlugins\eXpansion\LocalRecords\Gui\Widgets\LRPanel;
use \ManiaLivePlugins\eXpansion\LocalRecords\Config;

class LocalRecords extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    public static $players;
    private $records = array();
    private $lastRecord = null;

	function exp_onInit() {
		$this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_ROUNDS);
		$this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TIMEATTACK);
		$this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TEAM);
		$this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_CUP);
	}

    function exp_onLoad() {
        $this->enableDatabase();
        $this->enableDedicatedEvents();
        $this->enablePluginEvents();
        $this->setPublicMethod("getRecords");

        $this->registerChatCommand("save", "saveRecords", 0, true, \ManiaLive\Features\Admin\AdminGroup::get());
        $this->registerChatCommand("load", "loadRecords", 0, true, \ManiaLive\Features\Admin\AdminGroup::get());
        $this->registerChatCommand("reset", "resetRecords", 0, true, \ManiaLive\Features\Admin\AdminGroup::get());

        if (!$this->db->tableExists("exp_players")) {
            $this->db->execute('CREATE TABLE IF NOT EXISTS `exp_players` (  
  `login` varchar(255) NOT NULL,
  `nickname` text NOT NULL,
  PRIMARY KEY (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
        }

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
        foreach ($this->storage->players as $player)
            $this->onPlayerConnect($player->login, false);
        foreach ($this->storage->spectators as $player)
            $this->onPlayerConnect($player->login, true);

        $this->syncPlayers();
        $this->loadRecords($this->storage->currentMap->uId);
        $this->reArrage();

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
        $this->records = array_slice($newrecords, 0, 20);
        $this->lastRecord = end($this->records);

        if ($save)
            $this->saveRecords();

        LRPanel::$records = $this->records;
        LRPanel::EraseAll();


        $info = LRPanel::Create();
        $info->setSize(50, 60);
        $info->setPosition(-160, 30);
        $info->show();
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
            $this->reArrage(true);
            $this->announce($login);
        }

        // so if the time is better than the last entry or the count of records is less than 20...
        if ($this->lastRecord->time > $time || count($this->records) < 20) {
            // if player exists on the list... see if he got better time
            if (array_key_exists($login, $this->records)) {
                if ($this->records[$login]->time > $time) {
                    $oldRecord = $this->records[$login];
                    $this->records[$login] = new Structures\Record($login, $time);
                    $this->reArrage(true);
                    $this->announce($login, $oldRecord);
                    return;
                }
                // if not then just do a update for the time
            } else {
                $this->records[$login] = new Structures\Record($login, $time);
                $this->reArrage(true);
                $this->announce($login);
                return;
            }
        }
    }

    function announce($login, $oldRecord = null) {
        try {
            $player = $this->storage->getPlayerObject($login);
            $color = '$fff';
            $actionColor = '$0F0';

            if ($this->records[$login]->place == 1)
                $actionColor = '$0F0';

            $suffix = "th";

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
                $this->sendText('$o$03CC$04Co$06Dn$07Dg$08Er$09Ea$0BFt$0CFu$0CFl$1DFa$2DFt$3EFi$4EFo$5FFn$6FFs!$z$s ' . $actionColor . '$o' . $this->records[$login]->place . '$o' . $suffix . $color . '  for ' . $actionColor . \ManiaLib\Utils\Formatting::stripCodes($player->nickName, "wos") . '$z$s' . $color . ' with a time of $o' . $actionColor . \ManiaLive\Utilities\Time::fromTM($this->records[$login]->time) . '$o' . $color . ' $n(' . $diff . ')');
                return;
            }
            $this->sendText('$o$03CC$04Co$06Dn$07Dg$08Er$09Ea$0BFt$0CFu$0CFl$1DFa$2DFt$3EFi$4EFo$5FFn$6FFs!$z$s ' . $actionColor . '$o' . $this->records[$login]->place . '$o' . $suffix . $color . '  for ' . $actionColor . \ManiaLib\Utils\Formatting::stripCodes($player->nickName, "wos") . '$z$s' . $color . ' with a time of $o' . $actionColor . \ManiaLive\Utilities\Time::fromTM($this->records[$login]->time));
        } catch (\Exception $e) {
            \ManiaLive\Utilities\Console::println("Error: couldn't show localrecords message" . $e->getMessage());
        }
    }

    function sendText($text) {

        if (Config::getInstance()->sendNotices && $this->isPluginLoaded('eXpansion\Notifications')) {
            $this->callPublicMethod('eXpansion\Notifications', 'send', $text);
        } else {
            $this->connection->chatSendServerMessage($text);
        }
    }

    function syncPlayers() {
        $db = $this->db->query("Select * FROM exp_players")->fetchArrayOfAssoc();
        foreach ($db as $array)
            self::$players[$array['login']] = Structures\DbPLayer::fromArray($array);
    }

    function onPlayerConnect($login, $isSpectator) {
        $player = new Structures\DbPLayer();
        $player->fromPlayerObj($this->storage->getPlayerObject($login));
        $this->db->execute($player->exportToDb());
        self::$players[$login] = $player;
    }

    function onPlayerDisconnect($login) {
        
    }

}
?>