<?php

namespace ManiaLivePlugins\eXpansion\AutoQueue;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\String;

/**
 * Description of MetaData
 *
 * @author Petri
 * 
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData
{

    public function onBeginLoad()
    {
	parent::onBeginLoad();
	$this->setName("AutoQueue");
	$this->setDescription('AutoQueue for servers which has lot of players');
	
	$config = Config::getInstance();
	
    }

}

?>
