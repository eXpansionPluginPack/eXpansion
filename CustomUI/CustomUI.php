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
		Customizer::EraseAll();
		$info = Customizer::Create(null);
		$info->update();
		$info->setSize(60, 15);
		$info->show();
	}

	function onSettingsChanged(\ManiaLivePlugins\eXpansion\Core\types\config\Variable $var)
	{
		if ($var->getConfigInstance() instanceof \ManiaLivePlugins\eXpansion\CustomUI\Config) {
			$this->displayWidget(null);
		}
	}

	function exp_onUnload()
	{
		Customizer::EraseAll();
	}

}
?>

