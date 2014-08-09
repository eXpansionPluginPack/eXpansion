<?php

namespace ManiaLivePlugins\eXpansion\Players;

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
		$this->setName("Players");
		$this->setDescription("Provides Players list and common functions for players");

	}

}
