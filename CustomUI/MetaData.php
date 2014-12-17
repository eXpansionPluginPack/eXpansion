<?php

namespace ManiaLivePlugins\eXpansion\CustomUI;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;

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
		$this->setName("Game UI Elements Customizer");
		$this->setDescription("Enables you to hide game ui elements");
		$this->setGroups(array('UI'));

		$config = Config::getInstance();

		$var = new Boolean("overlayHideNotices", "Hide Notices", $config, false, false);
		$var->setDefaultValue(false);
		$this->registerVariable($var);

		$var = new Boolean("overlayHideMapInfo", "Hide Map Info", $config, false, false);
		$var->setDefaultValue(false);
		$this->registerVariable($var);

		$var = new Boolean("overlayHideOpponentsInfo", "Hide Opponents Info", $config, false, false);
		$var->setDefaultValue(false);
		$this->registerVariable($var);

		$var = new Boolean("overlayHideChat", "Hide Chatbox", $config, false, false);
		$var->setDefaultValue(false);
		$this->registerVariable($var);

		$var = new Boolean("overlayHideCheckPointList", "Hide CheckPoint List", $config, false, false);
		$var->setDefaultValue(false);
		$this->registerVariable($var);

		$var = new Boolean("overlayHideRoundScores", "Hide Round Scores", $config, false, false);
		$var->setDefaultValue(false);
		$this->registerVariable($var);

		$var = new Boolean("overlayHideCountdown", "Hide Countdown", $config, false, false);
		$var->setDefaultValue(false);
		$this->registerVariable($var);

		$var = new Boolean("overlayHideCrosshair", "Hide Crosshair", $config, false, false);
		$var->setDefaultValue(false);
		$this->registerVariable($var);

		$var = new Boolean("overlayHideGauges", "Hide Gauges", $config, false, false);
		$var->setDefaultValue(false);
		$this->registerVariable($var);

		$var = new Boolean("overlayHideConsumables", "Hide Consumables", $config, false, false);
		$var->setDefaultValue(false);
		$this->registerVariable($var);

		$var = new Boolean("overlayHide321Go", "Hide 321Go", $config, false, false);
		$var->setDefaultValue(false);
		$this->registerVariable($var);

		$var = new Boolean("overlayHideChrono", "Hide Chrono", $config, false, false);
		$var->setDefaultValue(false);
		$this->registerVariable($var);

		$var = new Boolean("overlayHideSpeedAndDist", "Hide Speed And Dist", $config, false, false);
		$var->setDefaultValue(false);
		$this->registerVariable($var);

		$var = new Boolean("overlayHidePersonnalBestAndRank", "Hide PersonnalBest And Rank", $config, false, false);
		$var->setDefaultValue(false);
		$this->registerVariable($var);

		$var = new Boolean("overlayHidePosition", "Hide Position", $config, false, false);
		$var->setDefaultValue(false);
		$this->registerVariable($var);
		
		$var = new Boolean("overlayHideCheckPointTime", "Hide CheckPoint Time", $config, false, false);
		$var->setDefaultValue(false);
		$this->registerVariable($var);

		$var = new Boolean("overlayChatHideAvatar", "Hide Chat Avatar", $config, false, false);
		$var->setDefaultValue(false);
		$this->registerVariable($var);

		$var = new \ManiaLivePlugins\eXpansion\Core\types\config\types\Int("overlayChatLineCount", "Chat Line Count", $config, false, false);
		$var->setDefaultValue(7);
		$this->registerVariable($var);
	}

}