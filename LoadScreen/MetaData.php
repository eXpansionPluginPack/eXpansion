<?php

namespace ManiaLivePlugins\eXpansion\LoadScreen;

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
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData
{

	public function onBeginLoad()
	{
		parent::onBeginLoad();
		$this->setName("Loading Screen");
		$this->setDescription("Provides customizable loadingscreens");
		$config = Config::getInstance();

		$var = new BasicList("screens", "List of LoadingScreens", $config, false);
		$var->setType(new String("", "", null));
		$var->setDefaultValue(array());
		$this->registerVariable($var);
	}

}
