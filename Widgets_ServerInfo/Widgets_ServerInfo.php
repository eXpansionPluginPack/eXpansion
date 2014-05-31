<?php

namespace ManiaLivePlugins\eXpansion\Widgets_ServerInfo;

class Widgets_ServerInfo extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    function exp_onLoad() {
	// $this->enableDedicatedEvents();
    }

    function exp_onReady() {
	$this->displayWidget();
    }

    /**
     * displayWidget()
     */
    function displayWidget() {
	$info = Gui\Widgets\ServerInfo::Create(null);
	$info->setSize(60, 15);
	$info->setPosition(-159, 89);
	$info->setScale(0.9);
	$info->setServerName($this->storage->server->name);
	$info->setLadderLimits($this->storage->server->ladderServerLimitMin, $this->storage->server->ladderServerLimitMax);
	$info->setPlayersCount($this->storage->server->currentMaxPlayers);
	$info->show();
    }

    public function onSettingsChanged(\ManiaLivePlugins\eXpansion\Core\types\config\Variable $var) {
	$this->displayWidget();
    }

    function exp_onUnload() {
	Clock::EraseAll();
    }

}
?>

