<?php

namespace ManiaLivePlugins\eXpansion\Notifications\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;

class Item extends \ManiaLivePlugins\eXpansion\Gui\Control {

    protected $label;
    protected $frame;

    function __construct($string) {
        $this->sizeX = 100;
        $this->sizeY = 3.5;
        $this->setAlign("left", "top");

        // $action = \ManiaLive\Gui\ActionHandler::getInstance()->createAction($item->callback);
        $this->label = new \ManiaLib\Gui\Elements\Label(100, 4);
        $this->label->setText($string);
        $this->label->setTextColor("fff");
        $this->label->setStyle("TextRaceChat");
        $this->addComponent($this->label);
    }

    protected function onResize($oldX, $oldY) {
        $this->frame->setSize($this->sizeX, $this->sizeY);
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target) {
        parent::onIsRemoved($target);
        $this->destroy();
    }

    public function destroy() {
        parent::destroy();
    }

}
?>

