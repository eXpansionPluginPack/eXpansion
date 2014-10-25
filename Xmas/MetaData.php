<?php

namespace ManiaLivePlugins\eXpansion\Xmas;

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
		$this->setName("Xmas lights for your server");
		$this->setDescription("Nice Xmas lights");
		$this->setGroups(array('UI', 'Widgets'));

	
	}

}
