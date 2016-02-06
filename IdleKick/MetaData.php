<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\IdleKick;

use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeString;

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
		$this->setName("Tools: Idle Kick");
		$this->setDescription("Automatically kicks the players who just idles at the server");
		$this->setGroups(array('Tools'));

		$config = Config::getInstance();

		$var = new \ManiaLivePlugins\eXpansion\Core\types\config\types\Float("idleMinutes", "Define here minutes after Idlekick should be performed ?", $config, false, false);
		$var->setDefaultValue(10);
		$this->registerVariable($var);

                $var = new \ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean("idleKickReally", "Really kick the player from server ?", $config, false, false);
                $var->setDescription("off = set spectator, on = really kick");
                $var->setDefaultValue(false);
		$this->registerVariable($var);
                
		$this->setRelaySupport(false);
	}

}
