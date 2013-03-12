<?php

namespace ManiaLivePlugins\eXpansion\RecordWidgets\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;
use ManiaLivePlugins\eXpansion\RecordWidgets\Gui\Controls\Recorditem;

class RecordsPanel extends \ManiaLive\Gui\Window {

    /** @var \ManiaLive\Gui\Controls\Frame */
    private $frame;

    public static $localrecords = array();
    
    public static $dedirecords = array();
    
    protected function onConstruct() {
        parent::onConstruct();

        $this->setAlign("left", "top");

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setAlign("left", "top");
        $this->frame->setPosition(3, -4);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(-1));
        $this->addComponent($this->frame);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
    }

    function onDraw() {
        echo "Draw \n";
        $index = 1;
        $this->frame->clearComponents();
        //foreach (self::$dedirecords as $record)
        //    $this->frame->addComponent(new Dediitem($index++, $record)); 
        foreach (self::$localrecords as $record)
            $this->frame->addComponent(new Recorditem($index++, $record));

        parent::onDraw();
    }

    function destroy() {
        $this->clearComponents();
        parent::destroy();
    }

}

?>
