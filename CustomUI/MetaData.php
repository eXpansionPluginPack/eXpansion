<?php

namespace ManiaLivePlugins\eXpansion\CustomUI;

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
		$this->setName("Game UI customizer");
		$this->setDescription("Custom Game UI");
	}

}
