<?php

namespace ManiaLivePlugins\eXpansion\Widgets_PlainLocalRecords;

use ManiaLive\PluginHandler\PluginHandler;
use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeString;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeInt;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedTypeInt;
use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeFloat;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedTypeFloat;

/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData
{

	public function onBeginLoad()
	{

		$this->setName("Widget: Plain Local Records");
		$this->setDescription("LocalRecords without maniascript");
		$this->setGroups(array('Widgets', 'Records'));

        	//$this->setEnviAsTitle(true);
		//$this->addTitleSupport('SM');
	}

}
