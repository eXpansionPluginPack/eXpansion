<?php

namespace ManiaLivePlugins\eXpansion\ForceSkin;

use \ManiaLivePlugins\eXpansion\Core\types\config\types\String;

/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData {

    public function onBeginLoad() {
	parent::onBeginLoad();
	$this->setName("Force Skin");
	$this->setDescription("Forces a skin to be used for all player on a server");

	$config = Config::getInstance();

	$var = new String("skinUrl", "skin url address", $config, false);
	$var->setDefaultValue("");
	$this->registerVariable($var);
	
	$var = new String("name", "set name for the skin", $config, false);
	$var->setDefaultValue("");
	$this->registerVariable($var);
    }

}
