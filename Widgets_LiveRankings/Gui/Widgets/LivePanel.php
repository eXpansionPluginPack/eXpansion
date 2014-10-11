<?php

namespace ManiaLivePlugins\eXpansion\Widgets_LiveRankings\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Structures\Script;

class LivePanel extends PlainLivePanel
{
	private $edgeWidget;
	private $animation;
	
	function exp_onBeginConstruct()
	{
		parent::exp_onBeginConstruct();
		/*
		$this->animation = new \ManiaLivePlugins\eXpansion\Gui\Script_libraries\Animation();
		$this->registerScript($this->animation);
		
		$this->edgeWidget = new Script("Gui/Scripts/EdgeWidget");
		$this->registerScript($this->edgeWidget);
		*/
	}

}

?>
