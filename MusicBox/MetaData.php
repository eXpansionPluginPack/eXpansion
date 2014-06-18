<?php

namespace ManiaLivePlugins\eXpansion\MusicBox;

use ManiaLivePlugins\eXpansion\Core\types\config\types\String;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Int;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedInt;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Float;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedFloat;

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
	$this->setName("Music Box");
	$this->setDescription("Provides custom music for your server");
	$config = Config::getInstance();

	$var = new Boolean("override", "Override all music on server, even if map has defined custom one ?", $config, false);
	$var->setDefaultValue(true);
	$var->setGroup("Musicbox");
	$this->registerVariable($var);

	$var = new String("url", "Enter tracklist url for musicbox ", $config, false);
	$var->setDefaultValue("http://reaby.kapsi.fi/ml/musictest");
	$var->setGroup("Musicbox");
	$this->registerVariable($var);
    }

}
