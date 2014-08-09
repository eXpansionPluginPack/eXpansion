<?php

namespace ManiaLivePlugins\eXpansion\Maps;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Int;
use ManiaLivePlugins\eXpansion\Core\types\config\types\SortedList;

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
		$this->setName("Maps");
		$this->setDescription("Provides common functions for maps, add,remove and list");

		$config = Config::getInstance();

		$var = new SortedList("publicQueueAmount", "Set Planets amounts for jukeboxing a map", $config);
		$var->setType(new Int("", "", null));
		$var->setDefaultValue(array(0));
		$this->registerVariable($var);

		$var = new Boolean("showNextMapWidget", "Show next map widget ?", $config, true, false);
		$var->setGroup("Widgets");
		$var->setDefaultValue(true);
		$this->registerVariable($var);

		$var = new Boolean("showEndMatchNotices", "Show end map notices ?", $config, true, false);
		$var->setGroup("Chat Messages");
		$var->setDefaultValue(true);
		$this->registerVariable($var);
	}

}
