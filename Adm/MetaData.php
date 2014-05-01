<?php

namespace ManiaLivePlugins\eXpansion\Adm;

use ManiaLivePlugins\eXpansion\Core\types\config\Variable;
use ManiaLivePlugins\eXpansion\Core\types\config\types\SortedList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Int;


/**
 * Description of MetaData
 *
 * @author De Cramer Oliver
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData{
    
    public function onBeginLoad() {
	parent::onBeginLoad();
	
	$this->setName("Server Control panel");
	$this->setDescription("Easy and graphical way of configuring your server.");
	
	$contentType = new Int("", "", null);	
	$config = Config::getInstance();
	
	$var = new SortedList('publicResAmount', 'Amount needed to restart a map', $config);
	$var->setGroup("Maps");
	$var->setDescription("If you use a negative value it will disable this feature.");
	$var->setType($contentType);
	$var->setDefaultValue(array(0 => 500));
	$this->registerVariable($var);
	
	$var = new SortedList('publicSkipAmount', 'Amount needed to skip a map', $config);
	$var->setGroup("Maps");
	$var->setDescription("If you use a negative value it will disable this feature.");
	$var->setType($contentType);
	$var->setDefaultValue(array(0 => 750));
	$this->registerVariable($var);
    }
}

?>
