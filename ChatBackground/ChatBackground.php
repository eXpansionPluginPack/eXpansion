<?php

namespace ManiaLivePlugins\eXpansion\ChatBackground;

class ChatBackground extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {
	
    public function exp_onReady() {
        $window = Gui\Windows\BoxWindow::Create(null);     
        $window->show();
    }

	public function exp_onUnload()
	{
		Gui\Windows\BoxWindow::EraseAll();
		parent::exp_onUnload();
	}
}

?>
