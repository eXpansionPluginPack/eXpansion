<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Map;

use ManiaLivePlugins\eXpansion\Widgets_Map\Gui\Widgets\Map;

class Widgets_Map extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

	function exp_onLoad()
	{
		// $this->enableDedicatedEvents();
	}

	function exp_onReady()
	{
		$this->displayWidget(null);
	}

	/**
	 * displayWidget(string $login)
	 * @param string $login
	 */
	function displayWidget($login)
	{
		$info = Gui\Widgets\Map::Create(null);
		$info->setSize(60, 15);
		//$info->setPosition(115, 89);
		$info->setScale(0.75);
		$info->show();
	}

	function exp_onUnload()
	{
		Map::EraseAll();
	}

}
?>

