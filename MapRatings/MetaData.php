<?php

namespace ManiaLivePlugins\eXpansion\MapRatings;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedTypeInt;

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
		$this->setName("Maps: Local Ratings");
		$this->setDescription("Provides ratings for maps");
		$this->setGroups(array('Maps'));

		$config = Config::getInstance();

		$var = new Boolean("sendBeginMapNotices", "Send Map ratings messages at begin of map ?", $config, true, false);
		$var->setDefaultValue(false);
		$this->registerVariable($var);

		$var = new Boolean("showPodiumWindow", "Show map ratings widget at podium ?", $config, true, false);
		$var->setDefaultValue(true);
		$this->registerVariable($var);

		$var = new BoundedTypeInt("minVotes", "Map Autoremoval required minimum votes (min: 5)", $config, true, false);
		$var->setGroup("Voting");
		$var->setMin(5);
		$var->setDefaultValue(10);
		$this->registerVariable($var);

		$var = new BoundedTypeInt("removeTresholdPercentage", "Map ratings autoremove percentage", $config, true, false);
		$var->setDescription("%-value for autoremove treshold (min: 10, max:60)");
		$var->setGroup("Voting");
		$var->setMin(10);
		$var->setMax(60);
		$var->setDefaultValue(30);
		$this->registerVariable($var);

		$this->setRelaySupport(false);
	}

}
