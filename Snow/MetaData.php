<?php

namespace ManiaLivePlugins\eXpansion\Snow;

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
		$this->setName("Widget: Snow");
		$this->setDescription("Seasonal widget: creates a slow falling snow effect");
		$this->setGroups(array('Widgets'));

                $config = Config::getInstance();
                $var = new \ManiaLivePlugins\eXpansion\Core\types\config\types\TypeString("texture", "texture url", $config, false, false);
                $var->setDefaultValue("http://reaby.kapsi.fi/ml/xmas/snowflake2.png");
                $this->registerVariable($var);

                $var = new \ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedTypeInt("particleCount", "Particles count", $config, false, false);
                $var->setDefaultValue(25);
                $var->setMin(1);
                $var->setMax(200);
                $this->registerVariable($var);
                

	}

}
