<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Controls;

class CustomPointEntry extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    private $bg;

    private $label;

    private $label2;

    private $frame;

    private $action;

    public function __construct($indexNumber, $points, $plugin, $login, $sizeX)
    {
        $sizeY = 6;

        $this->action = $this->createAction(array($plugin, "setPoints"), null);
        $this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround($indexNumber, $sizeX - 8, $sizeY);
        $this->addComponent($this->bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame(2, 0);
        $this->frame->setSize($sizeX - 8, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());


        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setAlign("center", "center2");
        $spacer->setStyle("Icons128x128_1");
        $spacer->setSubStyle("Challenge");
        $this->frame->addComponent($spacer);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        //$this->frame->addComponent($spacer);

        $this->label = new \ManiaLib\Gui\Elements\Label(40, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setText(__("Custom point", $login));
        $this->label->setScale(0.8);
        $this->frame->addComponent($this->label);


        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);

        $this->label2 = new \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox("customPoints", 90);
        $this->label2->setText($points);
        $this->frame->addComponent($this->label2);

        $this->addComponent($this->frame);

        $this->button = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
        $this->button->setText(__("Set", $login));
        $this->button->setAction($this->action);
        $this->button->setScale(0.6);
        $this->frame->addComponent($this->button);

        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
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
        $this->button->destroy();
        $this->frame->destroyComponents();
        $this->frame->destroy();
        $this->destroyComponents();
        parent::destroy();
    }
}
