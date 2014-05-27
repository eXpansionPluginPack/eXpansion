<?php

namespace ManiaLivePlugins\eXpansion\Widgets_TeamRoundScores;

use \ManiaLivePlugins\eXpansion\Core\types\config\types\String;

/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData {

    public function onBeginLoad() {
	parent::onBeginLoad();
	$this->setName("Round Score widget for teams mode");
	$this->setDescription("");

	$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM);
	$this->setScriptCompatibilityMode(false);
    }

}
