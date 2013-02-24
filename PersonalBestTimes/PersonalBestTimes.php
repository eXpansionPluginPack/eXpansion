<?php

namespace ManiaLivePlugins\eXpansion\PersonalBestTimes;

use ManiaLivePlugins\eXpansion\PersonalBestTimes\Gui\Widgets\PBPanel;

class PersonalBestTimes extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    public static $personalBestTimes = array();

    function exp_onInit() {
		$this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_ROUNDS);
		$this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TIMEATTACK);
		$this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TEAM);
		$this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_CUP);
    }

    function exp_onLoad() {
        $this->enableDatabase();
        $this->enableDedicatedEvents();       
        if (!$this->isPluginLoaded('eXpansion\LocalRecords'))
            die("Error, you MUST have enabled eXpansion\localrecords plugin to run this plugin!");

        if (!$this->db->tableExists("exp_pbtimes")) {
            $this->db->execute('CREATE TABLE IF NOT EXISTS `exp_pbtimes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` text NOT NULL,
  `login` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;');
        }
    }

    public function exp_onReady() {
        foreach ($this->storage->players as $player)
            $this->onPlayerConnect($player->login, false);
        foreach ($this->storage->spectators as $player)
            $this->onPlayerConnect($player->login, true);
    }

    public function save($login) {
        if (!isset(self::$personalBestTimes[$login])) {
            print "can't find $login in pbtimes for save.";
            return;
        }

        $player = self::$personalBestTimes[$login];

        $login = $this->db->quote($player->login);
        $uid = $this->db->quote($this->storage->currentMap->uId);
        $time = $this->db->quote($player->time);
        try {
            $test = $this->db->query("SELECT * FROM exp_pbtimes WHERE `uid`= " . $uid . "  AND `login` = " . $this->db->quote($login) . " LIMIT 1;")->fetchObject();

            if ($test === false) {
                $query = "INSERT INTO exp_pbtimes (`login`, `uid`, `time`) values ($login, $uid, $time);";
                $this->db->execute($query);
            } else {
                $query = "UPDATE exp_pbtimes set `time` = $time WHERE `login` = $login AND `uid` = $uid;";
                $this->db->execute($query);
            }
        } catch (\Exception $e) {
            \ManiaLive\Utilities\Console::println("Error in PBTimes: " . $e->getMessage());
        }
    }

    public function load($login) {
        $database = $this->db->query("SELECT * FROM exp_pbtimes WHERE `uid`=" . $this->db->quote($this->storage->currentMap->uId) . " AND `login` = " . $this->db->quote($login) . " LIMIT 1;")->fetchObject();
        if ($database !== false)
            self::$personalBestTimes[$login] = new Structures\BestTime($login, $database->time);
    }

    function displayWidget($login = null) {
        if ($login == null)
            PBPanel::EraseAll();
        else
            PBPanel::Erase($login);

        $info = PBPanel::Create($login);
        $info->setSize(30, 6);
        $info->setPosition(148, -68.5);
        $info->show();
    }

    function onBeginMap($map, $warmUp, $matchContinuation) {
        self::$personalBestTimes = array();
        foreach ($this->storage->players as $player)
            $this->onPlayerConnect($player->login, false);
        foreach ($this->storage->spectators as $player)
            $this->onPlayerConnect($player->login, true);

                
    }

    function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {
        PBPanel::EraseAll();
    }

    function onPlayerFinish($playerUid, $login, $time) {
        if ($time == 0)
            return;

        if (!isset(self::$personalBestTimes[$login]) || self::$personalBestTimes[$login]->time > $time) {

            self::$personalBestTimes[$login] = new Structures\BestTime($login, $time);
            $this->save($login);
            $this->displayWidget($login);
        }
    }

    function onPlayerConnect($login, $isSpectator) {
        $this->load($login);
        $this->displayWidget($login);
    }

    function onPlayerDisconnect($login) {
        PBPanel::Erase($login);
    }

}
?>

