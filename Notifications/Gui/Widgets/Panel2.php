<?php

namespace ManiaLivePlugins\eXpansion\Notifications\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Notifications\Gui\Controls\Item;

class Panel2 extends \ManiaLive\Gui\Window {

    private $frame;

    protected function onConstruct() {
        parent::onConstruct();
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setAlign("left", "bottom");
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(1));
        $this->addComponent($this->frame);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
    }

    function onShow() {
        
    }

    function setItems(array $menuItems) {
        $this->frame->clearComponents();

        foreach ($menuItems as $menuItem) {
            $item = new Item($menuItem);
            $this->frame->addComponent($item);
        }

        //$posY = abs(count($menuItems) * 6);
        //$this->frame->setPosition(6, $posY);        
    }

    function destroy() {
        parent::destroy();
    }

}

?>
