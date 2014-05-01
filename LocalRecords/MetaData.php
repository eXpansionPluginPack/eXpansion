<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords;

use ManiaLivePlugins\eXpansion\Core\types\config\types\String;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Int;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedInt;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Float;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedFloat;

/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData {

    public function onBeginLoad() {
	parent::onBeginLoad();
	$this->setName("Records");
	$this->setDescription("Provides local records for the server, uses mysql database to store records");

	$config = Config::getInstance();
	
	$var = new Boolean("sendBeginMapNotices", "Localrecords: show message at begin map", $config);
	$var->setGroup("Chat Messages");
	$var->setDefaultValue(false);
	$this->registerVariable($var);
	
	$var = new Boolean("sendRankingNotices", "Localrecords: Personal rankings messages at begin map", $config);
	$var->setGroup("Chat Messages");
	$var->setDefaultValue(false);
	$this->registerVariable($var);
	
	$var = new BoundedInt("recordsCount", "Localrecords: records count (min: 30)", $config);
	$var->setGroup("Records");
	$var->setMin(30);
	$var->setMax(1000);
	$var->setDefaultValue(100);
	$this->registerVariable($var);
	
	$var = new BoundedInt("recordPublicMsgTreshold", "Localrecords: Public chat messages to TOP x", $config);
	$var->setGroup("Records");
	$var->setDescription("to show always public messages, set this to same value as recordsCount");
	$var->setMin(1);
	$var->setMax(1000);
	$var->setDefaultValue(15);
	$this->registerVariable($var);

	
	$var = new Boolean("lapsModeCount1lap", "Localrecords: Count in 1st lap in Laps-mode ?", $config);
	$var->setGroup("Records");
	$var->setDefaultValue(true);
	$this->registerVariable($var);
	
	$var = new BoundedInt("rankRefresh", "Localrecords: refresh Ranking every x maps", $config);
	$var->setGroup("Records");
	$var->setMin(1);
	$var->setMax(10);
	$var->setDefaultValue(5);
	$this->registerVariable($var);
	
	$var = new Boolean("ranking", "Localrecords: Calculate local rankings for players ?", $config);
	$var->setGroup("Records");
	$var->setDefaultValue(true);
	$this->registerVariable($var);
	
	
	$var = new BoundedInt("nbMap_rankProcess", "Process rankings every <x> maps. (min: 1, max: 10)", $config);	
	$var->setGroup("Records");
	$var->setMin(1);
	$var->setMax(10);
	$var->setDefaultValue(15);
	
	

    }

}
