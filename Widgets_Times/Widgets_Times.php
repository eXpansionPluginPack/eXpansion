<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Times;

use ManiaLivePlugins\eXpansion\Widgets_Times\Gui\Widgets\TimePanel;
use ManiaLivePlugins\eXpansion\Widgets_Times\Gui\Widgets\TimeChooser;
use \ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\LocalRecords\Events\Event as LocalEvent;

class Widgets_Times extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $modes = array();
    private $audio = array();

    function exp_onInit() {
	//Important for all eXpansion plugins.
	$this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_ROUNDS);
	$this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TIMEATTACK);
	$this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TEAM);
	$this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_LAPS);
	$this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_CUP);
	TimeChooser::$plugin = $this;
    }

    function exp_onLoad() {
	$this->enableDedicatedEvents();
	if ($this->isPluginLoaded('eXpansion\Dedimania')) {
	    Dispatcher::register(\ManiaLivePlugins\eXpansion\Dedimania\Events\Event::getClass(), $this);
	}
	if ($this->isPluginLoaded('eXpansion\LocalRecords')) {
	    Dispatcher::register(LocalEvent::getClass(), $this, LocalEvent::ON_UPDATE_RECORDS);
	}
    }

    function exp_onReady() {
	$this->onBeginMatch();
    }

    public function onPlayerCheckpoint($playerUid, $login, $timeOrScore, $curLap, $checkpointIndex) {

	$mode = TimePanel::Mode_PersonalBest;
	if (isset($this->modes[$login])) {
	    if ($this->modes[$login] == TimePanel::Mode_None)
		return;
	    $mode = $this->modes[$login];
	}

	$playAudio = false;
	if (isset($this->audio[$login])) {
	    $playAudio = $this->audio[$login];
	}

	$info = TimePanel::Create($login);
	$info->onCheckpoint($timeOrScore, $checkpointIndex, $this->storage->currentMap->nbCheckpoints, $mode, $playAudio);
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
	$info->updatePanelMode($this->modes[$login], $this->audio[$login]);
    }

    public function setAudioMode($login, $audiomode) {
	$this->audio[$login] = $audiomode;
	$info = Gui\Widgets\TimeChooser::Create($login);
	$info->updatePanelMode($this->modes[$login], $this->audio[$login]);
    }

    public function onEndMatch($rankings, $winnerTeamOrMap) {
	TimePanel::EraseAll();
	TimeChooser::EraseAll();
    }

    public function onBeginMatch() {
	foreach ($this->storage->players as $player)
	    $this->showPanel($player->login);

	foreach ($this->storage->spectators as $player)
	    $this->showPanel($player->login, true);
    }

    function showPanel($login) {
	$widget = TimeChooser::Create($login);
	$widget->setLayer(\ManiaLive\Gui\Window::LAYER_NORMAL);
	$widget->setSize(40, 6);
	if (!isset($this->modes[$login]))
	    $this->modes[$login] = TimePanel::Mode_PersonalBest;

	if (!isset($this->audio[$login]))
	    $this->audio[$login] = false;
	$widget->updatePanelMode($this->modes[$login], $this->audio[$login]);

	$widget->setPosition(14, -78);
	$widget->show();
    }

    function onPlayerConnect($login, $isSpectator) {
	$audiopreload = Gui\Widgets\AudioPreload::Create($login);
	$audiopreload->show();
	$this->showPanel($login);
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

    public function onDedimaniaPlayerConnect($data) {
	
    }

    public function onDedimaniaPlayerDisconnect($login) {
	
    }

    /**
     * 
     * @param \ManiaLivePlugins\eXpansion\Dedimania\Structures\DediRecord $record
     * @param \ManiaLivePlugins\eXpansion\Dedimania\Structures\DediRecorr $oldrecord
     */
    public function onDedimaniaRecord($record, $oldrecord) {
	foreach (TimePanel::$dedirecords as $index => $data) {
	    if (TimePanel::$dedirecords[$index]['Login'] == $record->login) {
		TimePanel::$dedirecords[$index] = Array("Login" => $record->login, "NickName" => $record->nickname, "Rank" => $record->place, "Best" => $record->time, "Checks" => $record->checkpoints);
	    }
	}
    }

    /**
     * 
     * @param \ManiaLivePlugins\eXpansion\Dedimania\Structures\DediRecord $data
     */
    public function onDedimaniaNewRecord($record) {
	foreach (TimePanel::$dedirecords as $index => $data) {
	    if (TimePanel::$dedirecords[$index]['Login'] == $record->login) {
		TimePanel::$dedirecords[$index] = Array("Login" => $record->login, "NickName" => $record->nickname, "Rank" => $record->place, "Best" => $record->time, "Checks" => $record->checkpoints);
	    }
	}
    }

}
?>

