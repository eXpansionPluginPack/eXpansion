<?php

namespace ManiaLivePlugins\eXpansion\AutoQueue;

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
		$this->setGroups(array('Tools'));
		$config = Config::getInstance();
	}

}

?>
