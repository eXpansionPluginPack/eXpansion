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
            $this->doSeparator($item, $login);
            return;
        }

        $action = \ManiaLive\Gui\ActionHandler::getInstance()->createAction($item->callback);
        $button = new myButton(40, 6);
        $button->setScale(0.6);
        $button->setPosX(4);
        $button->setText(__($item->title, $login));
        $button->colorize('0000');
        $button->setTextColor('fff');
        $button->setAction($action);

        $this->addComponent($button);
    }

    protected function onResize($oldX, $oldY) {
        // $this->frame->setSize($this->sizeX, $this->sizeY);
    }

    function doSeparator($item, $login) {
        $this->sizeX = 30;
        $this->sizeY = 6;
        $bg = new \ManiaLib\Gui\Elements\Quad(50, 4);
        $bg->setPosition(-3, 1);
        $bg->setAlign("left", "top");

        $bg->setStyle('BgsPlayerCard');
        $bg->setSubStyle('BgRacePlayerName');
        $this->addComponent($bg);
        $label = new \ManiaLib\Gui\Elements\Label(30, 4);
        $label->setStyle("TextStaticVerySmall");
        $label->setTextColor('fff');
        $label->setAlign("left", "top");
        $label->setText(__($item->title, $login));
        $this->addComponent($label);
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target) {
        parent::onIsRemoved($target);
        $this->destroy();
    }

    function destroy() {
        parent::destroy();
    }
}
?>

