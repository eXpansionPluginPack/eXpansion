<?php

namespace ManiaLivePlugins\eXpansion\Adm;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Int;
use ManiaLivePlugins\eXpansion\Core\types\config\types\SortedList;

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

		$this->setName("Core: Graphical Admin");
		$this->setDescription("Easy and graphical way of configuring your server.");
		$this->setGroups(array('Core'));

		$config = Config::getInstance();
		
	}
}

?>
