<?php

namespace ManiaLivePlugins\eXpansion\MapRatings;

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
	$this->setName("Map Ratings");
	$this->setDescription("Provides ratings for maps");

	$config = Config::getInstance();
	
	$var = new Boolean("sendBeginMapNotices", "Send Map ratings messages at begin of map ?", $config);
	$var->setGroup("Chat Messages");
	$var->setDefaultValue(false);
	$this->registerVariable($var);
	
	$var = new Boolean("showPodiumWindow", "Show map ratings widget at podium ?", $config);
	$var->setGroup("Widgets");
	$var->setDefaultValue(true);
	$this->registerVariable($var);
	
	$var = new BoundedInt("minVotes", "Map Autoremoval required minimum votes (min: 5)", $config);
	$var->setGroup("Voting");
	$var->setMin(5);	
	$var->setDefaultValue(10);
	$this->registerVariable($var);
	
	$var = new BoundedInt("removeTresholdPercentage", "Map ratings autoremove percentage", $config);
	$var->setDescription("%-value for autoremove treshold (min: 10, max:60)");
	$var->setGroup("Voting");
	$var->setMin(10);
	$var->setMax(60);
	$var->setDefaultValue(30);
	$this->registerVariable($var);				
	
    }

}
