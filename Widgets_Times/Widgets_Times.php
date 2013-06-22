<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Times;

use \ManiaLivePlugins\eXpansion\Widgets_Times\Gui\Widgets\TimePanel;
use \ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\LocalRecords\Events\Event as LocalEvent;

class Widgets_Times extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    function exp_onInit() {
        //Important for all eXpansion plugins.
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_ROUNDS);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TIMEATTACK);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TEAM);
        //$this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_LAPS);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_CUP);
    }

    function exp_onLoad() {
        $this->enableDedicatedEvents();
        /* if ($this->isPluginLoaded('Reaby\Dedimania')) {
          Dispatcher::register(\ManiaLivePlugins\Reaby\Dedimania\Events\Event::getClass(), $this);
          } */
    }

    public function onPlayerCheckpoint($playerUid, $login, $timeOrScore, $curLap, $checkpointIndex) {
        $info = TimePanel::Create($login);
        $info->onCheckpoint($timeOrScore, $checkpointIndex);
        $info->setSize(30, 6);
        $info->centerOnScreen();
        $info->setPosY(40);
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

    public function onEndMatch($rankings, $winnerTeamOrMap) {
        TimePanel::EraseAll();
    }

    function onPlayerDisconnect($login, $reason = null) {
        TimePanel::Erase($login);
    }

}
?>

