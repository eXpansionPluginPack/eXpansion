<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedInt;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Int;

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
		$this->setName("Records");
		$this->setDescription("Provides local records for the server, uses mysql database to store records");

		//Listing the compatible Games
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_ROUNDS);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT, 'TeamAttack.Script.txt');

		$this->addTitleSupport("TM");
		$this->addTitleSupport("Trackmania");

		$config = Config::getInstance();

		$var = new BoundedInt("recordsCount", "Localrecords: records count (min: 30)", $config, true, false);
		$var->setMin(30);
		$var->setMax(1000);
		$var->setDefaultValue(100);
		$this->registerVariable($var);

		$var = new BoundedInt("recordPublicMsgTreshold", "Localrecords: Public chat messages to TOP x", $config, true, false);
		$var->setDescription("to show always public messages, set this to same value as recordsCount");
		$var->setMin(1);
		$var->setMax(1000);
		$var->setDefaultValue(15);
		$this->registerVariable($var);


		$var = new Boolean("lapsModeCount1lap", "Localrecords: Count in 1st lap in Laps-mode ?", $config, true, false);
		$var->setDefaultValue(true);
		$this->registerVariable($var);


		$var = new Boolean("ranking", "Localrecords: Calculate local rankings for players ?", $config, true, false);
		$var->setDefaultValue(true);
		$this->registerVariable($var);


		$var = new Int("nbMap_rankProcess", "Number of Maps to Process", $config);
		$var->setDescription("Number of consecutive maps for which ranking will be calculated at first start", true, false);
		$var->setDefaultValue(500);
		$this->registerVariable($var);

		$var = new Boolean("resetRanks", "Reset rankings(May take time)", $config);
		$var->setDescription("Will delete ranks for this server in order to recreate them. It may take time!!!", true, false);
		$var->setDefaultValue(false);
		$this->registerVariable($var);

		$var = new Boolean("sendBeginMapNotices", "Localrecords: show message at begin map", $config, true, false);
		$var->setGroup("Chat Messages");
		$var->setDefaultValue(false);
		$this->registerVariable($var);

		$var = new Boolean("sendRankingNotices", "Localrecords: Personal rankings messages at begin map", $config, true, false);
		$var->setGroup("Chat Messages");
		$var->setDefaultValue(false);
		$this->registerVariable($var);
	}

}
