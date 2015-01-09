<?php
namespace ManiaLivePlugins\eXpansion\MultiEnvRankings;

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
		$this->setName("MultiEnvRankings");
		$this->setDescription("Shows a manialink of your average on the MultiEnvironments.");

	}
}
