<?php

namespace ManiaLivePlugins\eXpansion\Debugtool;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\String;

/**
 * Description of MetaData
 *
 * @author De Cramer Oliver
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData{
    
    public function onBeginLoad() {
	parent::onBeginLoad();
	$this->setName("DebugTool");
	$this->setDescription('Connect / disconnect npc with ease!!');
	
    }
}

?>
