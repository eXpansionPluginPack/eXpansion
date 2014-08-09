<?php

namespace ManiaLivePlugins\eXpansion\CustomUI;

use ManiaLivePlugins\eXpansion\CustomUI\Gui\Customizer;

class CustomUI extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
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
	 *
	 * @param string $login
	 */
	function displayWidget($login)
	{
		$info = Customizer::Create(null);
		$info->setSize(60, 15);
		//$info->setPosition(115, 89);
		$info->setScale(0.75);
		$info->show();
	}

	function exp_onUnload()
	{
		Customizer::EraseAll();
	}

}

?>

