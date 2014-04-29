<?php

namespace ManiaLivePlugins\eXpansion\AutoUpdate;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;

/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData {

    public function onBeginLoad() {
	parent::onBeginLoad();
	$this->setName("Auto Update service");
	$this->setDescription("Provides auto update service requests and ingame updates");

	$config = Config::getInstance();
	$var = new Boolean("autoCheckUpdates", "Auto check updates service for new update on administator connect ingame ?", $config);	
	$var->setDefaultValue(true);
	$this->registerVariable($var);
    }

}
