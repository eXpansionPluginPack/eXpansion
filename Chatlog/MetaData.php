<?php

namespace ManiaLivePlugins\eXpansion\Chatlog;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Int;

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
		$this->setName("Chat log & history");
		$this->setDescription("Logs chat and provides ingame command /chatlog for viewing chat history");
		$this->setGroups(array('Chat', 'Tools'));

		$config = Config::getInstance();
		$var = new Int("historyLenght", "Chatlog history lenght", $config, false, false);
		$var->setCanBeNull(false)
			->setDefaultValue(100);
		$this->registerVariable($var);
	}

}
