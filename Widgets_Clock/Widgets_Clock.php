<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Clock;

use ManiaLive\Gui\Window;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Widgets_Clock\Gui\Widgets\Clock;

class Widgets_Clock extends ExpPlugin
{


    public function eXpOnReady()
    {
        $this->show();
    }

    public function show()
    {
        $widget = Clock::Create(null);
        $widget->setLayer(Window::LAYER_SCORES_TABLE);
        $widget->show();
    }

    public function eXpOnUnload()
    {
        Clock::EraseAll();
    }
}
