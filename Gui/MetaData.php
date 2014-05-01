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

	$var = new Boolean("disableAnimations", "Disable window animations", $config);
	$var->setGroup("Look & Feel");
	$var->setDefaultValue(false);
	$this->registerVariable($var);

	$var = new Boolean("disablePersonalHud", "Disable personalized hud", $config);
	$var->setDescription("if disable this, server admin defined positions are forced to all players");
	$var->setGroup("Look & Feel");
	$var->setDefaultValue(false);
	$this->registerVariable($var);

	$var = new String("style_widget_bgStyle", "Widgets: background style", $config);
	$var->setGroup("Widgets");
	$var->setDefaultValue("BgsPlayerCard");
	$this->registerVariable($var);

	$var = new String("style_widget_bgSubStyle", "Widgets: background substyle", $config);
	$var->setGroup("Widgets");
	$var->setDefaultValue("BgRacePlayerName");
	$this->registerVariable($var);

	$var = new String("style_widget_bgColorize", "Widgets: background color", $config);
	$var->setGroup("Widgets");
	$var->setDefaultValue("000");
	$this->registerVariable($var);

	$var = new BoundedFloat("style_widget_bgOpacity", "Widgets: background opacity", $config);
	$var->setGroup("Widgets");
	$var->setMin(0.0);
	$var->setMax(1.0);
	$var->setDefaultValue(1.0);
	$this->registerVariable($var);
    }

}
