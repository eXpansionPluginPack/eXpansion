<?php

namespace ManiaLivePlugins\eXpansion\Statistics\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button;

class Menu extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    public $frame;


    public function __construct()
    {
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($this->getSizeX(), 4);
        $this->frame->setPosY(0);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column());
        $this->addComponent($this->frame);
    }

    protected function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->frame->setSize($this->getSizeX(), 4);
    }

    public function addItem($label, $action, $color = null)
    {
        $button = new Button($this->getSizeX(), 6);
        $button->setText($label);
        $button->setAction($action);

        if ($color != null) {
            $button->colorize($color);
        }

        $this->frame->addComponent($button);
    }
}
