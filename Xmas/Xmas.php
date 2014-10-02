<?php

namespace ManiaLivePlugins\eXpansion\Xmas;

use ManiaLivePlugins\eXpansion\Xmas\Config;

class Xmas extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

	public function exp_onReady()
	{
		$window = Gui\Windows\XmasWindow::Create(null);
		$window->show();
	}

	public function exp_onUnload()
	{
		Gui\Windows\XmasWindow::EraseAll();
		parent::exp_onUnload();
	}

}

?>
