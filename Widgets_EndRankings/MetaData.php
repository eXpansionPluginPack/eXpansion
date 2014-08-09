<?php

namespace ManiaLivePlugins\eXpansion\Widgets_EndRankings;

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
		$this->setName("End map rankings widget");
		$this->setDescription("Provides end map rankings widget");

		//Important for all eXpansion plugins.
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_ROUNDS);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP);
	}

}
