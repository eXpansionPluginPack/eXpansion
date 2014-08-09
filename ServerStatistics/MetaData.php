<?php

namespace ManiaLivePlugins\eXpansion\ServerStatistics;

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
		$this->setName("Server Statistics");
		$this->setDescription("Provides advanced server statistics gathering and ingame viewer");

	}

}
