<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Times;

use ManiaLivePlugins\eXpansion\Widgets_Times\Gui\Widgets\TimePanel;
use ManiaLivePlugins\eXpansion\Widgets_Times\Gui\Widgets\TimeChooser;
use \ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\LocalRecords\Events\Event as LocalEvent;

class Widgets_Times extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $modes = array();

    function exp_onInit() {
        //Important for all eXpansion plugins.
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_ROUNDS);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TIMEATTACK);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TEAM);
        //$this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_LAPS);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_CUP);
        TimeChooser::$plugin = $this;
    }

    function exp_onLoad() {
        $this->enableDedicatedEvents();
        if ($this->isPluginLoaded('Reaby\Dedimania')) {
            Dispatcher::register(\ManiaLivePlugins\Reaby\Dedimania\Events\Event::getClass(), $this);
        }
        Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_UPDATE_RECORDS);
    }

    function exp_onReady() {
        $this->onBeginMap(null, null, null);
    }

    public function onPlayerCheckpoint($playerUid, $login, $timeOrScore, $curLap, $checkpointIndex) {
        $info = TimePanel::Create($login);
        $mode = TimePanel::Mode_PersonalBest;
        if (isset($this->modes[$login]))
            $mode = $this->modes[$login];
        $info->onCheckpoint($timeOrScore, $checkpointIndex, $mode);
        $info->setSize(30, 6);
        $info->setPosition(0, 40);
        $info->show();
    }

    public function onPlayerFinish($playerUid, $login, $timeOrScore) {
        if ($timeOrScore != 0) {
            $info = TimePanel::Create($login);
            $info->onFinish($timeOrScore);
            if (!$this->storage->currentMap->lapRace)
                $info->hide();
        }
        if ($timeOrScore == 0) {
            $info = TimePanel::Create($login);
            $info->onStart();
            $info->hide();
        }
    }

    public function setMode($login, $mode) {
        $this->modes[$login] = $mode;
        $info = Gui\Widgets\TimeChooser::Create($login);
        $info->updatePanelMode($mode);
    }

    public function onEndMatch($rankings, $winnerTeamOrMap) {
        TimePanel::EraseAll();
        TimeChooser::EraseAll();
    }

    public function onBeginMap($map, $warmUp, $matchContinuation) {
        foreach ($this->storage->players as $player)
            $this->onPlayerConnect($player->login, false);

        foreach ($this->storage->spectators as $player)
            $this->onPlayerConnect($player->login, true);
    }

    function onPlayerConnect($login, $isSpectator) {
        $widget = TimeChooser::Create($login);
        $widget->setSize(40, 6);
        $widget->setPosition(0, -77);
        $widget->show();
    }

    function onPlayerDisconnect($login, $reason = null) {
        TimePanel::Erase($login);
        TimeChooser::Erase($login);
    }

    public function onUpdateRecords($data) {
        TimePanel::$localrecords = $data;
    }

    public function onDedimaniaUpdateRecords($data) {
        TimePanel::$dedirecords = $data['Records'];
    }

    public function onDedimaniaGetRecords($data) {
        TimePanel::$dedirecords = $data['Records'];
    }

    public function onDedimaniaOpenSession() {
        
    }

    public function onNewRecord($data) {
        
    }

    public function onDedimaniaNewRecord($data) {
        
    }

    public function onDedimaniaPlayerConnect($data) {
        
    }

    public function onDedimaniaPlayerDisconnect() {
        
    }

    public function onDedimaniaRecord($record, $oldrecord) {
        
    }

}
?>

