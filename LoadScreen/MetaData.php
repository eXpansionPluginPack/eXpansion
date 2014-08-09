<?php

namespace ManiaLivePlugins\eXpansion\LoadScreen;

use ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedInt;
use ManiaLivePlugins\eXpansion\Core\types\config\types\String;

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

		$var = new BasicList("screens", "List of LoadingScreens", $config, false, false);
		$var->setType(new String("", "", null));
		$var->setDefaultValue(array());
		$this->registerVariable($var);

		$var = new BoundedInt("screensDelay", "Show loading screen after [x] seconds of podium", $config, false, false);
		$var->setMin(1);
		$var->setDefaultValue(17);
		$this->registerVariable($var);
		
		$var = new Boolean("screensMx", "Use map image from MX as loading screen, if available", $config, false, false);
		$var->setDefaultValue(false);
		$this->registerVariable($var);
		
		
	}

}
