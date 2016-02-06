<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedTypeInt;
use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeInt;

/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData
{
	/**
	 * Do not change, if you use this you break compatibility. Plz fixing this on the other plugins for the third time now
	 */
	final public function onBeginLoad()
	{
		parent::onBeginLoad();

		$this->setGroups(array('Records'));

		$this->initName();

		$this->initCompatibility();

		$this->initSettings();
	}

	protected function initName(){
		$this->setName("Records: Local records Trackmania modes");
		$this->setDescription("Provides local records for the server, uses mysql database to store records");
	}

	protected function initCompatibility()
	{

		//Listing the compatible Games
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_ROUNDS);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT, 'TeamAttack.Script.txt');

		$this->addTitleSupport("TM");
		$this->addTitleSupport("Trackmania");
	}

	protected function initSettings(){
		$config = Config::getInstance();

		$var = new BoundedTypeInt("recordsCount", "Localrecords: records count (min: 30)", $config, true, false);
		$var->setMin(30);
		$var->setMax(1000);
		$var->setDefaultValue(100);
		$this->registerVariable($var);

		$var = new BoundedTypeInt("recordPublicMsgTreshold", "Localrecords: Public chat messages to TOP x", $config, true, false);
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


		$var = new TypeInt("nbMap_rankProcess", "Number of Maps to Process", $config);
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

		$var = new TypeInt('saveRecFrequency', 'Records save Frequency', $config, true, false);
		$var->setDefaultValue(0);
		$var->setDescription('Save every X minutes records. If 0 then will save on match end Only.');
		$this->registerVariable($var);
	}

}
