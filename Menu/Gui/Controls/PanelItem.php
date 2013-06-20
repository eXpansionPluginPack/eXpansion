<?php

namespace ManiaLivePlugins\eXpansion\Menu\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;

class PanelItem extends \ManiaLive\Gui\Control {

    private $bg;
    private $nick;
    private $label;
    private $time;
    private $frame;

    function __construct(\ManiaLivePlugins\eXpansion\Menu\Structures\Menuitem $item, $login) {
        $this->sizeX = 30;
        $this->sizeY = 5;
        $this->setAlign("left", "top");

        if ($item->isSeparator) {
            $this->doSeparator($item);
            return;
        }

        $action = \ManiaLive\Gui\ActionHandler::getInstance()->createAction($item->callback);
        $button = new myButton(30, 6);
        $button->setScale(0.6);
        $button->setText(__($item->title, $login));
        $button->setAction($action);

        $this->addComponent($button);
    }

    protected function onResize($oldX, $oldY) {
        $this->frame->setSize($this->sizeX, $this->sizeY);
    }

    function doSeparator($item) {
        $this->sizeY = 6;
        $this->sizeX = 30;
        $bg = new \ManiaLib\Gui\Elements\Quad(40, 6);
        $bg->setBgcolor('0007');
        // $this->addComponent($bg);
        $label = new \ManiaLib\Gui\Elements\Label(30, 4);
        $label->setStyle("TextCardInfoSmall");
        $label->setScale(0.9);
        $label->setText('$fff' . $item->title);
        $this->addComponent($label);
    }

    function onDraw() {
        
    }

    function __destruct() {
        
    }

}
?>

