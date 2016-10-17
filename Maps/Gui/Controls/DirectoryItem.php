<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Controls;

use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Gui\Gui;

class DirectoryItem extends \ManiaLivePlugins\eXpansion\Gui\Control
{
    protected $bg;

    protected $mapNick;

    protected $changeDirButton;

    protected $deleteButton;

    protected $label;

    protected $time;

    protected $changeDirAction;

    protected $deleteActionf;

    protected $deleteAction;

    protected $frame;

    public function __construct($indexNumber, $label, $filename, $controller, $login, $sizeX)
    {
        $sizeY = 6;

        $this->changeDirAction = $this->createAction(array($controller, 'changeDirectory'), $filename);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $layout = new \ManiaLib\Gui\Layouts\Line();
        $layout->setMargin(1, 0);
        $this->frame->setLayout($layout);

        $this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround($indexNumber, $sizeX, $sizeY);
        $this->bg->setAction($this->changeDirAction);
        $this->addComponent($this->bg);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setAlign("left", "center2");
        $spacer->setStyle("Icons128x128_1");
        $spacer->setSubStyle("Challenge");
        $this->frame->addComponent($spacer);

        $this->label = new \ManiaLib\Gui\Elements\Label(120, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setText(Gui::fixString($label));
        $this->label->setScale(0.8);
        $this->frame->addComponent($this->label);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        $this->frame->addComponent($spacer);


        $this->changeDirButton = new MyButton(24, 5);
        $this->changeDirButton->setText(__("Open", $login));
        $this->changeDirButton->setAction($this->changeDirAction);
        $this->changeDirButton->setScale(0.5);
        $this->changeDirButton->colorize("2a2");
        $this->frame->addComponent($this->changeDirButton);

        $this->addComponent($this->frame);
        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY)
    {
        $this->bg->setSize($this->sizeX, $this->sizeY);
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
        $this->changeDirButton->destroy();
        $this->destroyComponents();
        $this->destroy();
        parent::destroy();
    }
}
