<?php

namespace ManiaLivePlugins\eXpansion\Quiz;

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
		$this->setName("Quiz");
		$this->setDescription("Run a Quiz powered by questions made up by players");

	}

}
