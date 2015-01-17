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
		$this->setName("Widget: Snow");
		$this->setDescription("Seasonal widget: creates a slow falling snow effect");
		$this->setGroups(array('Widgets'));

	}

}
