<?php

namespace ManiaLivePlugins\eXpansion\Halloween;

/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData {

    public function onBeginLoad() {
	parent::onBeginLoad();
	$this->setName("Widget: Halloween");
	$this->setDescription("Seasonal widget, creates spiders!");
	$this->setGroups(array('Widgets'));

	$config = Config::getInstance();

	$var = new \ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedInt("spriteCount", "Spiders count", $config, false, false);	
	$var->setMin(1);
	$var->setMax(20);
	$var->setDefaultValue(3);
	
	$this->registerVariable($var);
    }

}
