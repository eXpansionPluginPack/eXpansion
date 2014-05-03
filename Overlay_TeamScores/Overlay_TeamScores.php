<?php

namespace ManiaLivePlugins\eXpansion\Overlay_TeamScores;

class Overlay_TeamScores extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    /** @var Structures\Team[] */
    private $teams = array();

    /** @var Maniaplanet\DedicatedServer\Structures\Status */
    private $status;
    private $action, $action2;
    private $access;
    private $clublinks = array("", "");

    public function exp_onInit() {
	$this->exp_addTitleSupport("TM");
	$this->exp_addTitleSupport("Trackmania");
        $this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM);
    }

    public function exp_onLoad() {
        $this->enableDedicatedEvents();
        $this->access = \ManiaLivePlugins\eXpansion\Core\DataAccess::getInstance();
    }

    public function exp_onReady() {
        $scores = $this->connection->getCurrentRanking(1, 0);
        $this->teams[0] = new Structures\Team($scores[0]->nickName);
        $this->teams[0]->score = $scores[0]->score;
        $scores = $this->connection->getCurrentRanking(1, 1);
        $this->teams[1] = new Structures\Team($scores[0]->nickName);
        $this->teams[1]->score = $scores[0]->score;


        Gui\Widgets\ScoresOverlay::$action = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, "setData0"));
        Gui\Widgets\ScoresOverlay::$action2 = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, "setData1"));
        Gui\Widgets\ScoresOverlay::$resetAction = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, "reset"));
        Gui\Widgets\ScoresOverlay::$toggleAction = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, "toggle"));

        if (self::exp_getCurrentCompatibilityGameMode() == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM) {
            foreach ($this->storage->spectators as $player) {
                $this->showWidget($player->login);
            }
            /* foreach ($this->storage->players as $player) {
              $this->showWidget($player->login);
              } */
        }
    }

    public function setData0($login) {

        $player = $this->storage->getPlayerObject($login);
        if (empty($player->clubLink))
            return;
        if (substr($player->clubLink, 0, 4) !== "http")
            return;
        $this->clublinks[0] = $player->clubLink;

        $this->connection->setForcedClubLinks($this->clublinks[0], $this->clublinks[1]);
        $this->syncWidget(0);
    }

    public function setData1($login) {
        $player = $this->storage->getPlayerObject($login);
        if (empty($player->clubLink))
            return;
        if (substr($player->clubLink, 0, 4) !== "http")
            return;
        $this->clublinks[1] = $player->clubLink;
        $this->connection->setForcedClubLinks($this->clublinks[0], $this->clublinks[1]);
        $this->syncWidget(1);
    }

    public function reset($login) {
        $scores = $this->connection->getCurrentRanking(-1, 0);
        $this->teams[0]->name = $scores[0]->nickName;
        $this->teams[1]->name = $scores[1]->nickName;
        $this->showWidget();
    }

    public function toggle($login) {
        Gui\Widgets\ScoresOverlay::$status = !Gui\Widgets\ScoresOverlay::$status;
        $this->showWidget();
    }

    function syncWidget($team) {
        if (empty($this->clublinks[$team]))
            return;
        if (substr($this->clublinks[$team], 0, 4) !== "http")
            return;
        $ch = curl_init($this->clublinks[$team]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        $output = curl_exec($ch);
        curl_close($ch);
        $xml = simplexml_load_string($output);
        // print_r($xml);
        $this->teams[$team]->name = $xml->name;
        $this->showWidget();
    }

    public function showWidget($login = null) {
        if (self::exp_getCurrentCompatibilityGameMode() != \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM)
            return;
        if ($login == null) {
            /* foreach ($this->storage->players as $login => $player) {
              $widget = Gui\Widgets\ScoresOverlay::Create($login);
              $widget->setData($this->teams);
              $widget->show();
              } */
            foreach ($this->storage->spectators as $login => $player) {
                $widget = Gui\Widgets\ScoresOverlay::Create($login);
                $widget->setData($this->teams);
                $widget->show();
            }
            return;
        }

        $widget = Gui\Widgets\ScoresOverlay::Create($login);
        $widget->setData($this->teams);
        $widget->show($login);
    }

    public function hideWidget($login = null) {

        if ($login == null) {
            Gui\Widgets\ScoresOverlay::EraseAll();
            return;
        }
        $widget = Gui\Widgets\ScoresOverlay::Erase($login);
    }

    public function onPlayerConnect($login, $isSpectator) {
        if (self::exp_getCurrentCompatibilityGameMode() != \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM)
            return;
        if ($isSpectator)
            $this->showWidget($login);
    }

    public function onPlayerInfoChanged($playerInfo) {
        if (self::exp_getCurrentCompatibilityGameMode() != \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM)
            return;
        $player = \Maniaplanet\DedicatedServer\Structures\PlayerInfo::fromArray($playerInfo);

        if ($player->spectator == 1)
            $this->showWidget($player->login);
        if ($playerInfo['SpectatorStatus'] == 0)
            $this->hideWidget($playerInfo['Login']);
    }

    public function onEndMatch($rankings, $winnerTeamOrMap) {
        $this->hideWidget();
    }

    public function onBeginRound() {
        $this->syncRanking();
    }

    public function onEndRound() {
        $this->syncRanking();
    }

    public function syncRanking() {
        if (self::exp_getCurrentCompatibilityGameMode() != \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM)
            return;
        $scores = $this->connection->getCurrentRanking(2, 0);
        $this->teams[0]->score = $scores[0]->score;
        $this->teams[1]->score = $scores[1]->score;
        $this->showWidget();
    }

}
