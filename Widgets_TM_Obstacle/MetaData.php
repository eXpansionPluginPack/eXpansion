<?php

namespace ManiaLivePlugins\eXpansion\Widgets_TM_Obstacle;

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
		$this->setName("Obstacle Progress Widget");
		$this->setDescription("Obstacle progress");
		$this->setGroups(array('UI', 'Widgets'));
	}

}
