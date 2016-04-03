<?php

namespace ManiaLivePlugins\eXpansion\Core\Gui\Controls;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button;

class ExpSettingsMenu extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    public $frame;


    function __construct()
    {
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($this->getSizeX(), 4);
        $this->frame->setPosition(-2, 0);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column());
        $this->addComponent($this->frame);
    }

    protected function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->frame->setSize($this->getSizeX() / 0.8, 4);
    }

    public function reset()
    {
        $this->frame->destroyComponents();
    }

    public function addItem($label, $action, $color = null)
    {
        $button = new Button($this->getSizeX() / 0.8, 6);
        $button->setText($label);
        $button->setAction($action);

        if ($color != null) {
            $button->colorize($color);
        }

        $this->frame->addComponent($button);
    }

}

?>
