<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

class dummy extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    protected function onConstruct()
    {
        parent::onConstruct();

        // $this->mainFrame->addComponent($component);
    }

    protected function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
    }

    protected function onShow()
    {

    }

    public function destroy()
    {
        parent::destroy();
    }
}
