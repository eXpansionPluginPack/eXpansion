<?php

namespace ManiaLivePlugins\eXpansion\Snow;

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
		$this->setName("Snow for your server");
		$this->setDescription("Snow");

	
	}

}
