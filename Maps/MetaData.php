<?php

namespace ManiaLivePlugins\eXpansion\Maps;

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
	$this->setName("Maps");
	$this->setDescription("Provides common functions for maps, add,remove and list");

	$config = Config::getInstance();

	$var = new Boolean("showNextMapWidget", "Show next map widget ?", $config, false);
	$var->setGroup("Widgets");
	$var->setDefaultValue(true);
	$this->registerVariable($var);

	$var = new Boolean("showEndMatchNotices", "Show end map notices ?", $config, false);
	$var->setGroup("Chat Messages");
	$var->setDefaultValue(true);
	$this->registerVariable($var);

	$var = new BasicList("publicQueueAmount", "Set Planets amounts for jukeboxing a map", $config, false);
	$var->setGroup("Maps");
	$var->setType(new Int("", "", null));
	$var->setDefaultValue(array(0));
	$this->registerVariable($var);
    }

}
