<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups;

use ManiaLivePlugins\eXpansion\Core\types\config\types\String;

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
		$this->setName("Admin groups");
		$this->setDescription("Provides admin groups operations, all plugins need this");
		$this->setGroups(array('Core', 'Admin'));

		$var = new String('fileName', 'Admin Groups data file', Config::getInstance());
		$var->setDescription("If left empty the name will be generated using the server login.");
		$var->setGroup("Config Files");
		$var->setCanBeNull(true)
			->setDefaultValue(null);
		$this->registerVariable($var);
	}
}

?>
