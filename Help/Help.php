<?php

namespace ManiaLivePlugins\eXpansion\Help;

use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Help\Gui\Windows\HelpWindow;

class Help extends ExpPlugin
{

    public function eXpOnReady()
    {
        $this->registerChatCommand("test", "display", 0, false);
        $this->display();
    }


    public function display()
    {
        $win = HelpWindow::Create();
        $win->setSize(220, 93);
        $win->setTitle("Help");
        $win->show();
    }

}
