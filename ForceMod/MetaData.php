<?php

namespace ManiaLivePlugins\eXpansion\ForceMod;


use ManiaLivePlugins\eXpansion\Core\types\config\types\HashList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\String;

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
		$this->setName("Force Mod");
		$this->setDescription("Forces a Mod for a server");

		$config = Config::getInstance();

		$var = new HashList("mods", "mods to be loaded", $config, false, false);
		$var->setKeyType(new String(""));
		$var->setType(new String(""));
		$var->setDefaultValue(array());
		$this->registerVariable($var);

		$this->setRelaySupport(false);
	}

}
