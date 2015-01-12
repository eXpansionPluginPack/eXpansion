<?php

namespace ManiaLivePlugins\eXpansion\Widgets_DedimaniaRecords\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Script_libraries\Animation;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;

class DediPanel extends PlainPanel
{

	private $edgeWidget;

	private $animation;

	function exp_onBeginConstruct()
	{
		parent::exp_onBeginConstruct();
		/*
		  $this->animation = new Animation();
		  $this->registerScript($this->animation);
		*/
		  $this->edgeWidget = new Script("Gui/Scripts/EdgeWidget");
		  $this->registerScript($this->edgeWidget);

	}

}

?>
