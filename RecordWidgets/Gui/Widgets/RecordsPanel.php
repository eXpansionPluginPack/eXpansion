<?php

namespace ManiaLivePlugins\eXpansion\RecordWidgets\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;
use ManiaLivePlugins\eXpansion\RecordWidgets\Gui\Controls\Recorditem;
use ManiaLivePlugins\eXpansion\RecordWidgets\Gui\Controls\DediItem;
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
        $index = 1;
        $this->frame->clearComponents();
        $lbl = new \ManiaLib\Gui\Elements\Label(20,5);
        $lbl->setAlign("left", "center");
        $lbl->setText('$fffDedimania Records');
        $lbl->setScale(0.9);
        $this->frame->addComponent($lbl);
        foreach (self::$dedirecords as $record) {
            if ($index > 30) return;
            $this->frame->addComponent(new DediItem($index++, $record)); 
            
        }
        /*foreach (self::$localrecords as $record)
            $this->frame->addComponent(new Recorditem($index++, $record)); */

        parent::onDraw();
    }

    function destroy() {
        $this->frame->clearComponents();
        $this->frame->destroy();        
        $this->clearComponents();
        parent::destroy();
    }

}

?>
