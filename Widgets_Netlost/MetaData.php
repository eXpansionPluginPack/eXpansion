<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Netlost;

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
		$this->setName("Network status widget");
		$this->setDescription("Provides netlost infos for admins");
	}

}
