<?php

namespace ManiaLivePlugins\eXpansion\Overlay_Positions;

use \ManiaLivePlugins\eXpansion\Core\Structures\ExpPlayer;

/**
 * Description of Overlay_Positions
 *
 * @author Reaby
 */
class Overlay_Positions extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $update = true;

    function exp_onInit() {
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_ROUNDS);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_TEAM);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_LAPS);
        $this->exp_addGameModeCompability(\DedicatedApi\Structures\GameInfos::GAMEMODE_CUP);
    }

    public function exp_onReady() {
        $this->enableDedicatedEvents();
        $this->enableTickerEvent();
    }

    public function onTick() {
        if ($this->update) {
            $this->update = false;
            foreach ($this->storage->players as $login => $player) {
                $this->showWidget($login);
            }
        }
    }

    public function showWidget($login) {
        $pospanel = Gui\Widgets\PositionPanel::Create($login);
        $pospanel->setData(\ManiaLivePlugins\eXpansion\Core\Core::$playerInfo);
        $pospanel->setPosition(-158, 0);
        $pospanel->show();
    }

    public function onPlayerCheckpoint($playerUid, $login, $timeOrScore, $curLap, $checkpointIndex) {
        $this->update = true;
    }

    public function onPlayerFinish($playerUid, $login, $timeOrScore) {
        $this->update = true;
    }

    public function onPlayerGiveup(ExpPlayer $player) {
        $this->update = true;
    }

}
