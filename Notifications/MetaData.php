<?php

namespace ManiaLivePlugins\eXpansion\Notifications;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
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
		$this->setName("Notifications");
		$this->setDescription("Notifications window for redirecting chat messages");
		$this->setGroups(array('Tools'));

		$config = Config::getInstance();

		$var = new \ManiaLivePlugins\eXpansion\Notifications\types\NotificationPluginlist("redirectedPlugins", "plugins to redirect", $config, false,false);
		$var->setType(new String(""));
		$var->setDefaultValue(array());
		$this->registerVariable($var);
		
	}

}