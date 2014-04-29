<?php

namespace ManiaLivePlugins\eXpansion\ForceMod;

use \ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList;

/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData {

    public function onBeginLoad() {
	parent::onBeginLoad();
	$this->setName("Force Mod");
	$this->setDescription("Forces a Mod for a server");

	$config = Config::getInstance();

	$var = new BasicList("mods", "mods to be loaded", $config, false);
	$var->setType(new String("", "", null));
	$var->setDefaultValue(array());
	$this->registerVariable($var);
    }

}
