<?php

namespace ManiaLivePlugins\eXpansion\Notifications\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;

class Item extends \ManiaLive\Gui\Control {

    private $bg;
    private $nick;
    private $label;
    private $time;
    private $frame;

    function __construct(\ManiaLivePlugins\eXpansion\Notifications\Structures\Item $item) {
        $this->sizeX = 100;
        $this->sizeY = 4;
        $this->setAlign("left", "top");

       // $action = \ManiaLive\Gui\ActionHandler::getInstance()->createAction($item->callback);
        $label = new \ManiaLib\Gui\Elements\Label(100, 5);        
        $label->setText($item->message);  
        $label->setTextColor("fff");
        $label->setStyle("TextRaceChat");
        $this->addComponent($label);
    }

    protected function onResize($oldX, $oldY) {
        $this->frame->setSize($this->sizeX, $this->sizeY);
    }   

    function onDraw() {
        
    }
    
    function __destruct() {
        
    }

}
?>

