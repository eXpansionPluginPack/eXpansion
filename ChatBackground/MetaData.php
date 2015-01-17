<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\ChatBackground;

use ManiaLivePlugins\eXpansion\Core\types\config\types\ColorCode;
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
		$this->setName("Chat: Background widget for chat");
		$this->setDescription("Background box for chat");
		$this->setGroups(array('Chat', 'Widgets'));

		$config = Config::getInstance();

		$var = new String('color', 'Main color', $config, false, false);
		$var->setDefaultValue('000');
		$this->registerVariable($var);
		
		$var = new String('colorHighlite', 'Highlight color', $config, false, false);
		$var->setDefaultValue('3af');
		$this->registerVariable($var);
	}

}
