<?php

namespace ManiaLivePlugins\eXpansion\Widgets_ServerInfo;

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
		$this->setName("Server infos widget");
		$this->setDescription("Provides server infos widget");
	}

}
