<?php

namespace ManiaLivePlugins\eXpansion\TM_Stunts;

use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedTypeInt;
use ManiaLivePlugins\eXpansion\Core\types\config\types\ColorCode;
use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeString;

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
		$this->setName("Widget: Stunts Figures");
		$this->setDescription("Displays the stunts you made for TM");
		$this->setGroups(array('Widgets'));

		$this->addTitleSupport("TM");
		$this->addTitleSupport("Trackmania");
		$this->setEnviAsTitle(false);

		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT, "TimeAttack.Script.txt");
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT, "Rounds.Script.txt");
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT, "Cup.Script.txt");
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT, "Team.Script.txt");
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT, "Laps.Script.txt");
		$this->setScriptCompatibilityMode(false);

		$this->setRelaySupport(false);

		$config = Config::getInstance();
	}

}

?>
