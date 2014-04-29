<?php

namespace ManiaLivePlugins\eXpansion\Gui;

use ManiaLivePlugins\eXpansion\Core\types\config\types\String;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Float;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedFloat;

/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData {

    public function onBeginLoad() {
	parent::onBeginLoad();
	$this->setName("Graphical user interface");
	$this->setDescription("");

	$config = Config::getInstance();

	$var = new Boolean("disableAnimations", "disable window animations", $config, false);
	$var->setDefaultValue(false);
	$this->registerVariable($var);

	$var = new Boolean("disablePersonalHud", "disable players possibility for personalize hud and force server hud configurations.", $config, false);
	$var->setDefaultValue(false);
	$this->registerVariable($var);
	
	$var = new String("style_widget_bgStyle", "widget background style", $config, false);
	$var->setDefaultValue("BgsPlayerCard");
	$this->registerVariable($var);
	
	$var = new String("style_widget_bgSubStyle", "widget background substyle", $config, false);
	$var->setDefaultValue("BgRacePlayerName");
	$this->registerVariable($var);
	
	$var = new String("style_widget_bgColorize", "widget background color", $config, false);
	$var->setDefaultValue("000");
	$this->registerVariable($var);
	
	$var = BoundedFloat("style_widget_bgOpacity", "widget background color", $config, false);	
	$var->setMin(0.0);
	$var->setMax(1.0);
	$var->setDefaultValue(1.0);
	$this->registerVariable($var);
	
	
	
    }

}
