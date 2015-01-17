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
		$this->setName("Widget: Obstacle Progress");
		$this->setDescription("Shows Checkpoint progress for 10 players in a widget");
		$this->setGroups(array('Widgets'));

		$this->addTitleSupport("TM");
		$this->addTitleSupport("Trackmania");
	}

}
