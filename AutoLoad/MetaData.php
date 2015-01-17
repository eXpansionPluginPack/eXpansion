<?php

namespace ManiaLivePlugins\eXpansion\AutoLoad;

use ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\String;
use ManiaLivePlugins\eXpansion\Core\types\config\Variable;

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
		$this->setName("Core: AutoLoader");
		$this->setDescription('Autoloader, all-in-one solution for loading eXpansion easily');
		$this->setGroups(array('Core'));

		$config = Config::getInstance();
		$type = new String("", "", null);

		$var = new BasicList('plugins', "Plugins to autoload", $config, Variable::SCOPE_FILE);
		$var->setType($type);
		$var->setVisible(false);
		$this->registerVariable($var);
	}
}
