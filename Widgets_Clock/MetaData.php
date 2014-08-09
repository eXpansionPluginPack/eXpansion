<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Clock;

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
		$this->setName("Clock and Mapinfos widget");
		$this->setDescription("Provides clock and map infos widget");
	}

}
