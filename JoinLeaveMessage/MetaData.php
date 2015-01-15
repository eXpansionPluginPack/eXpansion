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

		$config = Config::getInstance();
		$var = new \ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean("showTotalPlayOnJoin", "Show Total playtime on join ?", $config, false, false);
		$var->setDefaultValue(true);
		$this->registerVariable($var);

		$var = new \ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean("showLeaveMessage", "Show Leave messages ?", $config, false, false);
		$var->setDefaultValue(true);
		$this->registerVariable($var);
	}

}
