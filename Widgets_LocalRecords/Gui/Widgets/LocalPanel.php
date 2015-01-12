<?php

namespace ManiaLivePlugins\eXpansion\Widgets_LocalRecords\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Structures\Script;

class LocalPanel extends PlainPanel
{

	private $edgeWidget;

	protected function exp_onBeginConstruct()
	{

		parent::exp_onBeginConstruct();
		/*
		$animation = new \ManiaLivePlugins\eXpansion\Gui\Script_libraries\Animation();
		$this->registerScript($animation);
		*/
		$this->edgeWidget = new Script("Gui/Scripts/EdgeWidget");
		$this->registerScript($this->edgeWidget);
		
	}

}

?>