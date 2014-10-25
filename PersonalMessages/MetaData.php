<?php

namespace ManiaLivePlugins\eXpansion\PersonalMessages;

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
		$this->setName("Personal messages");
		$this->setDescription("Provides personal messaging");
		$this->setGroups(array('Chat'));

	}

}
