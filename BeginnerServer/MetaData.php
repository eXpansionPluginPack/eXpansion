<?php

namespace ManiaLivePlugins\eXpansion\BeginnerServer;

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
		$this->setName("Beginner server");
		$this->setDescription("Denies high ranked players from playing");
	}

}
