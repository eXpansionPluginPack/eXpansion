<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

class dummy extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    protected function onConstruct()
    {
        parent::onConstruct();

        // $this->mainFrame->addComponent($component);
    }

    function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
    }

    function onShow()
    {

    }

    function destroy()
    {
        parent::destroy();
    }

}

?>
