<?php

namespace ManiaLivePlugins\eXpansion\Adm;


/**
 * Description of MetaData
 *
 * @author De Cramer Oliver
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData
{

	public function onBeginLoad()
	{
		parent::onBeginLoad();

		$this->setName("Server Control panel");
		$this->setDescription("Easy and graphical way of configuring your server.");

		$this->setGroups(array('Core', 'Admin'));
	}
}

?>
