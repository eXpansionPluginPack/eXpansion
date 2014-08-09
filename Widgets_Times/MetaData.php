<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Times;

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
		$this->setName("Times tracking widget for players");
		$this->setDescription("Provides enhanced times tracking widget at center of screen");

		$this->addTitleSupport("TM");
		$this->addTitleSupport("Trackmania");

		//Important for all eXpansion plugins.
		//$this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_ROUNDS);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS);
		//$this->exp_addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP);
	}

}
