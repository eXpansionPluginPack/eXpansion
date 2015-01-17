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
		$this->setName("Widget: Top Info panel");
		$this->setDescription("Top infos panel");
		$this->setGroups(array('Widgets'));

		$this->addTitleSupport("TM");
		$this->addTitleSupport("Trackmania");

	}

}
