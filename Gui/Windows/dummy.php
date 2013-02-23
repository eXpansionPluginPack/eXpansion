<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;
use ManiaLive\Gui\ActionHandler;

class dummy extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    protected function onConstruct() {
        parent::onConstruct();

        // $this->mainFrame->addComponent($component);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
    }

    function onShow() {
        
    }

    function destroy() {
        parent::destroy();
    }

}

?>
