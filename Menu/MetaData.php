<?php

namespace ManiaLivePlugins\eXpansion\Menu;

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
		$this->setName("Core: Menu");
		$this->setDescription("Provides right click menu for the server");
		$this->setGroups(array('Core'));

	}

}
