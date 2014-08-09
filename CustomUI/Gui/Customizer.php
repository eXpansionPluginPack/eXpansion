<?php

namespace ManiaLivePlugins\eXpansion\CustomUI\Gui;

use ManiaLib\Gui\Elements\Label;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget;


class Customizer extends PlainWidget
{

	protected function onConstruct()
	{
		parent::onConstruct();
		$this->setName("Customizer");
		$script = new Script("CustomUI\Gui\Script");
		$this->registerScript($script);
	}
	
	function destroy()
	{
		$this->clearComponents();
		parent::destroy();
	}

}

?>
