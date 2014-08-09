<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\Database;

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
		$this->setName("Database interface");
		$this->setDescription("Handles eXpansion database tables versions, backups, restores and repairs etc");
		$config = Config::getInstance();

		$var = new \ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean("showWins", "Show player win statistics at podium ?", $config);
		$var->setGroup("Chat Messages");
		$var->setDefaultValue(true)
			->setCanBeNull(false);

		$this->registerVariable($var);
	}

}
