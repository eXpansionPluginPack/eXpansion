<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Controls;

use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Gui\Gui;

class NewAddItem extends \ManiaLivePlugins\eXpansion\Gui\Control
{
    protected $bg;

    protected $mapNick;

    protected $addButton;

    protected $deleteButton;

    protected $label;

    protected $time;

    protected $addMapAction;

    protected $deleteActionf;

    protected $deleteAction;

    protected $frame;

    public function __construct($indexNumber, $label, $filename, $controller, $login, $sizeX)
    {
        $sizeY = 6;
        $this->addMapAction = $this->createAction(array($controller, 'addMap'), $filename);
        $this->deleteActionf = $this->createAction(array($controller, 'deleteMap'), $filename);
        $this->deleteAction = \ManiaLivePlugins\eXpansion\Gui\Gui::createConfirm($this->deleteActionf);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $layout = new \ManiaLib\Gui\Layouts\Line();
        $layout->setMargin(1, 0);
        $this->frame->setLayout($layout);

        $this->label = new \ManiaLib\Gui\Elements\Label(120, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setText(Gui::fixString($label));
        $this->label->setScale(0.8);
        $this->frame->addComponent($this->label);

        $this->addButton = new MyButton(24, 5);
        $this->addButton->setText(__("Add", $login));
        $this->addButton->setAction($this->addMapAction);
        $this->addButton->setScale(0.5);
        $this->addButton->colorize("2a2");
        $this->frame->addComponent($this->addButton);

        $this->deleteButton = new MyButton(24, 5);
        $this->deleteButton->setAction($this->deleteAction);
        $this->deleteButton->setScale(0.5);
        $this->deleteButton->setText('$ff0' . __("Delete", $login));
        $this->deleteButton->colorize("222");
        $this->frame->addComponent($this->deleteButton);

        $this->addComponent($this->frame);
        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY)
    {
        $this->frame->setSize($this->sizeX, $this->sizeY);
    }

    // manialive 3.1 override to do nothing.
    public function destroy()
    {

    }

    /*
     * custom function to remove contents.
     */
    public function erase()
    {
        ActionHandler::getInstance()->deleteAction($this->deleteAction);
        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->addButton->destroy();
        $this->deleteButton->destroy();

        $this->destroyComponents();

        $this->destroy();
        parent::destroy();
    }
}
