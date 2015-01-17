<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Map;

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
		$this->setName("Widget: Current map");
		$this->setDescription("Displays simple map infos widget at top right corner");
		$this->setGroups(array('Widgets', 'Maps'));
	}

}
