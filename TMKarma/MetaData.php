<?php

namespace ManiaLivePlugins\eXpansion\TMKarma;

use ManiaLivePlugins\eXpansion\Core\types\config\types\String;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Int;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedInt;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Float;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedFloat;

/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData {

    public function onBeginLoad() {
	parent::onBeginLoad();
	$this->setName("TM-karma");
	$this->setDescription("Provides integration for TM-karma.com");
	
	$config = Config::getInstance();
	$var = New String("contryCode", "3-letter country code for the server (leave empty for autosense)", $config);
	$var->setDefaultValue("");
	$this->registerVariable($var);
	
	
    }

}
