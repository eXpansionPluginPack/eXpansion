<?php

namespace ManiaLivePlugins\eXpansion\Widgets_AroundMe;

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
		$this->setName("Widget: Around Me");
		$this->setDescription("Provides Around Me time display widget");
		$this->setGroups(array('Records', 'Widgets'));

		$this->addTitleSupport("TM");
		$this->addTitleSupport("Trackmania");
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_ROUNDS);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP);
	}

}
