<?php

namespace ManiaLivePlugins\eXpansion\Widgets_TM_topPanel;

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
		$this->setName("Top panel");
		$this->setDescription("Top panel");
		$this->setGroups(array('UI', 'Widgets'));

		$this->addTitleSupport("TM");
		$this->addTitleSupport("Trackmania");

	}

}
