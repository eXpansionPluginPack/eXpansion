<?php

namespace ManiaLivePlugins\eXpansion\Dedimania_Script;

use \ManiaLivePlugins\eXpansion\Core\types\config\types\String;

/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData {
    
    public function onBeginLoad() {
	parent::onBeginLoad();
	$this->setName("Dedimania (script mode)");
	$this->setDescription("Dedimania, Global world records system integration");

	$config	= Config::getInstance();
	
	$var = new String("login", "Dedimania server login (use this server login)", $config, false);
	$var->setDefaultValue("");
	$this->registerVariable($var);
	
	$var = new String("code", 'Dedimania $l[http://dedimania.net/tm2stats/?do=register]server code$l', $config, false);	
	$var->setDefaultValue("");
	$this->registerVariable($var);
    }
}
