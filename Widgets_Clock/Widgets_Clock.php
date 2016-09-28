<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Clock;

use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Widgets_Clock\Gui\Widgets\Clock;

class Widgets_Clock extends ExpPlugin
{


    public function eXpOnReady()
    {
        $this->registerChatCommand("s", "show");
        $this->show();
    }

    public function show()
    {
        $widget = Clock::Create(null);
        $widget->show();
    }

    public function eXpOnUnload()
    {
        Clock::EraseAll();
    }
}
