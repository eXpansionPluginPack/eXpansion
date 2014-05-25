<?php

namespace ManiaLivePlugins\eXpansion\Autoload;

use ManiaLivePlugins\eXpansion\Core\types\config\types\String;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList;
/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData {

    public function onBeginLoad() {
	parent::onBeginLoad();
	$this->setName("AutoLoad");
	$this->setDescription('Autoloader, all-in-one solution for loading eXpansion easily');
	$config = Config::getInstance();
	$type = new String("","",null);

	$var = new BasicList('plugins', "Plugins to autoload", $config);
	$var->setType($type);
	$var->setVisible(false);
	$this->registerVariable($var);
    }
}
