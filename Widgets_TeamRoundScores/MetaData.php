<?php

namespace ManiaLivePlugins\eXpansion\Widgets_TeamRoundScores;

/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData
{

	public function onBeginLoad()
	{
		parent::onBeginLoad();
		$this->setName("Round Score widget for teams mode");
		$this->setDescription("");
		$this->setGroups(array('UI', 'Widgets'));

		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM);
		$this->setScriptCompatibilityMode(false);
	}

}
