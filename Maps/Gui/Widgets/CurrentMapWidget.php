<?php
namespace ManiaLivePlugins\eXpansion\Maps\Gui\Widgets;

class CurrentMapWidget extends \ManiaLive\Gui\Window {
    
    protected function onConstruct() {
        $label = new \ManiaLib\Gui\Elements\Label();        
        $label->setText('$ddd'.__('Current Map'));
        $label->setAlign("right", "top");                
        $this->addComponent($label);
        
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
