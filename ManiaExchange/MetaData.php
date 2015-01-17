<?php

namespace ManiaLivePlugins\eXpansion\ManiaExchange;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedFloat;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedInt;

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
		$this->setName("Maps: Mania-Exchange integration");
		$this->setDescription("Provides integration for Mania-Exchange.com");
		$this->setGroups(array('Maps', 'Connectivity'));

		$config = Config::getInstance();


		$var = new Boolean("juke_newmaps", "Autojuke newly added maps ?", $config, true, false);
		$var->setDefaultValue(true);
		$this->registerVariable($var);

		$var = new Boolean("mxVote_enable", "Allow players to temporarily add maps using votes ?", $config, false, false);
		$var->setGroup("Voting");
		$var->setDefaultValue(false);
		$this->registerVariable($var);

		$var = new BoundedFloat("mxVote_ratio", "MXVote ratio for adding map", $config, true, false);
		$var->setGroup("Voting");
		$var->setMin(-1.0);
		$var->setMax(1.0);
		$var->setDefaultValue(0.5);
		$this->registerVariable($var);

		$var = new BoundedInt("mxVote_timeout", "MXVote Timeout in seconds", $config, true, false);
		$var->setDescription("Min: 30, Max: 360");
		$var->setGroup("Voting");
		$var->setMin(30);
		$var->setMax(360);
		$var->setDefaultValue(false);
		$this->registerVariable($var);


		$var = new BoundedInt("mxVote_voters", "MXVote Limit", $config, true, false);
		$var->setGroup("Voting");
		$var->setMin(0);
		$var->setMax(2);
		$var->setDefaultValue(1);
		$this->registerVariable($var);
	}

}
