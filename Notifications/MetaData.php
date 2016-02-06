<?php

namespace ManiaLivePlugins\eXpansion\Notifications;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Float;
use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeString;

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
		$this->setName("Chat: Chat Notifications redirect");
		$this->setDescription("Redirect chat messages coming from plugins to a widget");
		$this->setGroups(array('Chat', 'Widgets'));

		$config = Config::getInstance();

		$var = new \ManiaLivePlugins\eXpansion\Notifications\types\NotificationPluginlist("redirectedPlugins", "plugins to redirect", $config, false,false);
		$var->setType(new TypeString(""));
		$var->setDefaultValue(array());
		$this->registerVariable($var);

		$var = new Float("posX", "Pos X", $config, false,false);
		$var->setDefaultValue(40);
		$var->setDefaultValue(array());
		$this->registerVariable($var);

		$var = new Float("posY", "Pos Y", $config, false,false);
		$var->setDefaultValue(-40);
		$var->setDefaultValue(array());
		$this->registerVariable($var);

	}

}