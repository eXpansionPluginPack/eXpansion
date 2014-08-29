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

		$this->setName("Server Control panel");
		$this->setDescription("Easy and graphical way of configuring your server.");

		$this->setGroups(array('Core', 'Admin'));

		$config = Config::getInstance();

		for($i = 0; $i < 20; $i++){
			$var = new SortedList("customPoints".($i+1), "Custom Points ".($i+1), $config, true, true);
			$var->setType(new Int("", "", null));
			$var->setDefaultValue(array(0));
			$var->setOrder("desc");
			$var->setGroup("Custom Round Points");
			$this->registerVariable($var);
		}
	}
}

?>
