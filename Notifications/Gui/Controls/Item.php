<?php

namespace ManiaLivePlugins\eXpansion\Notifications\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;

class Item extends \ManiaLive\Gui\Control {

    private $label;
    private $frame;

    function __construct($string) {
        $this->sizeX = 100;
        $this->sizeY = 3.5;
        $this->setAlign("left", "top");

        // $action = \ManiaLive\Gui\ActionHandler::getInstance()->createAction($item->callback);
        $label = new \ManiaLib\Gui\Elements\Label(100, 4);
        $label->setText($string);
        $label->setTextColor("fff");
        $label->setStyle("TextRaceChat");
        $this->addComponent($label);
    }

    protected function onResize($oldX, $oldY) {
        $this->frame->setSize($this->sizeX, $this->sizeY);
    }

}
?>

