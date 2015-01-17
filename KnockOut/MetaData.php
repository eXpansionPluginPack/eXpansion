<?php

namespace ManiaLivePlugins\eXpansion\KnockOut;

use ManiaLivePlugins\eXpansion\Core\types\config\types\String;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Int;
use ManiaLivePlugins\eXpansion\Core\types\config\types\ColorCode;

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
		$this->setName("GameMode: KnockOut!");
		$this->setDescription("Provides Knockout Virtual Game mode");
		
		$this->addTitleSupport("TM");
		$this->addTitleSupport("Trackmania");
		$this->setScriptCompatibilityMode(false);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_ROUNDS);

		$configInstance = Config::getInstance();

		$var = new ColorCode("koColor", "Color for knockout", $configInstance, false, false);
		$var->setDefaultValue('$0d0');
		$this->registerVariable($var);

		$this->setRelaySupport(false);
	}

}
