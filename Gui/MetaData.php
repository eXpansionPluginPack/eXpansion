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
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData
{

	public function onBeginLoad()
	{
		parent::onBeginLoad();
		$this->setName("Graphical user interface");
		$this->setDescription("");

		$config = Config::getInstance();
		$var = new String("uiTextureBase", "Texture base for ui ", $config);
		$var->setGroup("Look & Feel");
		$var->setDefaultValue("http://reaby.kapsi.fi/ml/ui");
		$this->registerVariable($var);

		$var = new String("windowTitleColor", "Color for window titlebar", $config);
		$var->setDescription("you can use short 3 (+1 for alpha) or full 6 (+2 for alpha) color code for this value");
		$var->setGroup("Look & Feel");
		$var->setDefaultValue("000a");
		$this->registerVariable($var);

		$var = new String("buttonTitleColor", "Color for button texts", $config);
		$var->setDescription("you can use short 3 (+1 for alpha) or full 6 (+2 for alpha) color code for this value");
		$var->setGroup("Look & Feel");
		$var->setDefaultValue("000a");
		$this->registerVariable($var);


		$var = new Boolean("disableAnimations", "Disable window animations", $config);
		$var->setGroup("Look & Feel");
		$var->setDefaultValue(false);
		$this->registerVariable($var);

		$var = new Boolean("disablePersonalHud", "Disable personalized hud", $config);
		$var->setDescription("if disable this, server admin defined positions are forced to all players");
		$var->setGroup("Look & Feel");
		$var->setDefaultValue(false);
		$this->registerVariable($var);

		$var = new String("style_widget_bgStyle", "background style", $config);
		$var->setGroup("Widgets");
		$var->setDefaultValue("BgsPlayerCard");
		$this->registerVariable($var);

		$var = new String("style_widget_bgSubStyle", "background substyle", $config);
		$var->setGroup("Widgets");
		$var->setDefaultValue("BgRacePlayerName");
		$this->registerVariable($var);

		$var = new String("style_widget_bgColorize", "background color", $config);
		$var->setGroup("Widgets");
		$var->setDefaultValue("000");
		$this->registerVariable($var);

		$var = new BoundedFloat("style_widget_bgOpacity", "background opacity", $config);
		$var->setGroup("Widgets");
		$var->setMin(0.0);
		$var->setMax(1.0);
		$var->setDefaultValue(1.0);
		$this->registerVariable($var);

		$var = new String("style_widget_title_bgStyle", "Title background style", $config);
		$var->setGroup("Widgets");
		$var->setDefaultValue("UiSMSpectatorScoreBig");
		$this->registerVariable($var);

		$var = new String("style_widget_title_bgSubStyle", "Title background substyle", $config);
		$var->setGroup("Widgets");
		$var->setDefaultValue("PlayerSlotCenter");
		$this->registerVariable($var);

		$var = new String("style_widget_title_bgColorize", "Title background color", $config);
		$var->setGroup("Widgets");
		$var->setDefaultValue("3af");
		$this->registerVariable($var);

		$var = new String("style_widget_title_lbStyle", "Title label style", $config);
		$var->setGroup("Widgets");
		$var->setDefaultValue("TextCardScores2");
		$this->registerVariable($var);

		$var = new BoundedFloat("style_widget_title_lbSize", "Title label size", $config);
		$var->setGroup("Widgets");
		$var->setMin(0.5);
		$var->setMax(5.0);
		$var->setDefaultValue(1);
		$this->registerVariable($var);
	}

}
