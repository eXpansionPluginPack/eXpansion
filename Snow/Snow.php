<?php

namespace ManiaLivePlugins\eXpansion\Snow;

use ManiaLivePlugins\eXpansion\Snow\Config;

class Snow extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

	public function exp_onReady()
	{
		$window = Gui\Windows\SnowParticle::Create(null);
		$window->show();
	}

	public function exp_onUnload()
	{
		Gui\Windows\SnowParticle::EraseAll();
		parent::exp_onUnload();
	}

}

?>
