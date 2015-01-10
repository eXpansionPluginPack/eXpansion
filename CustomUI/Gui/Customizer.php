<?php

namespace ManiaLivePlugins\eXpansion\CustomUI\Gui;

use ManiaLivePlugins\eXpansion\CustomUI\Config;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget;

class Customizer extends PlainWidget
{

	protected function onConstruct()
	{
		parent::onConstruct();
		$this->setName("Customizer");
		$this->script = new Script("CustomUI\Gui\Script");
		$this->registerScript($this->script);
	}

	public function update()
	{
		$config = \ManiaLivePlugins\eXpansion\CustomUI\Config::getInstance();
		$this->script->setParam("OverlayHideNotices", $this->getBoolean($config->overlayHideNotices));
		$this->script->setParam("OverlayHideMapInfo", $this->getBoolean($config->overlayHideMapInfo));
		$this->script->setParam("OverlayHideOpponentsInfo", $this->getBoolean($config->overlayHideOpponentsInfo));
		$this->script->setParam("OverlayHideChat", $this->getBoolean($config->overlayHideChat));
		$this->script->setParam("OverlayHideCheckPointList", $this->getBoolean($config->overlayHideCheckPointList));
		$this->script->setParam("OverlayHideRoundScores", $this->getBoolean($config->overlayHideRoundScores));
		$this->script->setParam("OverlayHideCountdown", $this->getBoolean($config->overlayHideCountdown));
		$this->script->setParam("OverlayHideCrosshair", $this->getBoolean($config->overlayHideCrosshair));
		$this->script->setParam("OverlayHideGauges", $this->getBoolean($config->overlayHideGauges));
		$this->script->setParam("OverlayHideConsumables", $this->getBoolean($config->overlayHideConsumables));
		$this->script->setParam("OverlayHide321Go", $this->getBoolean($config->overlayHide321Go));
		$this->script->setParam("OverlayHideChrono", $this->getBoolean($config->overlayHideChrono));
		$this->script->setParam("OverlayHideSpeedAndDist", $this->getBoolean($config->overlayHideSpeedAndDist));
		$this->script->setParam("OverlayHidePersonnalBestAndRank", $this->getBoolean($config->overlayHidePersonnalBestAndRank));
		$this->script->setParam("OverlayHidePosition", $this->getBoolean($config->overlayHidePosition));
		$this->script->setParam("OverlayHideCheckPointTime", $this->getBoolean($config->overlayHideCheckPointTime));
		$this->script->setParam("OverlayChatHideAvatar", $this->getBoolean($config->overlayChatHideAvatar));
		$this->script->setParam("OverlayChatLineCount", intval($config->overlayChatLineCount));
	}

	function destroy()
	{
		$this->destroyComponents();
		parent::destroy();
	}

}

?>
