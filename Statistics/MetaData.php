<?php

namespace ManiaLivePlugins\eXpansion\Statistics;

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
		$this->setName("Player Statistics");
		$this->setDescription("Provides advanced player statistics");

	}

}
