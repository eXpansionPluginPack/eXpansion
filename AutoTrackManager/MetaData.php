<?php

namespace ManiaLivePlugins\eXpansion\AutoTrackManager;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Int;

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
		$this->setName("AutoTrackManager");
		$this->setDescription("Auto Removes Tracks / Maps");

		$config = Config::getInstance();
		
		$var = new Int('MINVotes', "Minimal [INT] Votes needed to remove a track", $config, false);
        $var->setGroup("AutoTrackManager");
        $this->registerVariable($var);

		$var = new Int('integervalue', "Integer [INT] Percentage to remove a track", $config, false);
        $var->setGroup("AutoTrackManager");
        $this->registerVariable($var);
	}

}
