<?php

namespace ManiaLivePlugins\eXpansion\JoinLeaveMessage;

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
		$this->setName("Join and Leave messages");
		$this->setDescription("Provides chat messages for joining and leaving players");

		$this->setRelaySupport(false);
	}

}
