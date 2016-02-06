<?php

namespace ManiaLivePlugins\eXpansion\Widgets_LiveRankings\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Structures\Script;

class LivePanel extends PlainLivePanel
{
        protected $trayWidget;
	
	function exp_onBeginConstruct()
	{
		parent::exp_onBeginConstruct();
		
		$this->trayWidget = new Script("Gui/Scripts/NewTray");
		$this->registerScript($this->trayWidget);
		
	}

}

?>
