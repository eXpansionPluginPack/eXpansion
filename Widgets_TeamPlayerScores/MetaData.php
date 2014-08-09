<?php

namespace ManiaLivePlugins\eXpansion\Widgets_TeamPlayerScores;

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
		$this->setName("Player Score widget for teams mode");
		$this->setDescription("");

		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM);
		$this->setScriptCompatibilityMode(false);
	}

}
