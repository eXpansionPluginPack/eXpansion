<?php

namespace ManiaLivePlugins\eXpansion\Widgets_LocalScores;

use ManiaLive\PluginHandler\PluginHandler;
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
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData
{

	public function onBeginLoad()
	{

		$this->setName("Widget: Local Scores");
		$this->setDescription("Local scores widget, can be used when local records are in points instead of time");
		$this->setGroups(array('Widgets', 'Records'));

		//$this->setEnviAsTitle(true);
		//$this->addTitleSupport('SM');
	}

}
