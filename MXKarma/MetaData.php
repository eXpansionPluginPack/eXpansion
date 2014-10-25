<?php

namespace ManiaLivePlugins\eXpansion\MXKarma;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\String;

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
		$this->setName("MX karma");
		$this->setDescription("Provides integration for Karma.Mania-Exchange.com");
		$this->setGroups(array('Maps'));

		$config = Config::getInstance();

		$var = new String("mxKarmaApiKey", "MxKarma apikey", $config, false, false);
		$var->setDefaultValue("");
		$this->registerVariable($var);

		$var = new String("mxKarmaServerLogin", "MxKarma serverlogin", $config, false, false);
		$var->setDefaultValue("");
		$this->registerVariable($var);

		$this->setRelaySupport(false);
	}

}
