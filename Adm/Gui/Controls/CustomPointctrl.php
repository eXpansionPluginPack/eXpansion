<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Controls;

use ManiaLib\Gui\Elements\Icons64x64_1;
use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Control;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;

class CustomPointctrl extends Control
{
    /** @var ListBackGround */
    protected $bg;
    /** @var Label */
    protected $label;
    /** @var Label */
    protected $label2;
    /** @var Frame */
    protected $frame;
    /** @var Button */
    protected $button;

    /** @var integer */
    protected $action;

    public function __construct($indexNumber, $point, $plugin, $login, $sizeX)
    {
        $sizeY = 6;

        $this->action = $this->createAction(array($plugin, "setPoints"), $point->points);
        $this->bg = new ListBackGround($indexNumber, $sizeX - 8, $sizeY);
        $this->addComponent($this->bg);

        $this->frame = new Frame(2, 0);
        $this->frame->setSize($sizeX - 8, $sizeY);
        $this->frame->setLayout(new Line());


        $spacer = new Quad();
        $spacer->setSize(4, 4);
        $spacer->setAlign("center", "center2");
        $spacer->setStyle("Icons128x128_1");
        $spacer->setSubStyle("Challenge");
        $this->frame->addComponent($spacer);

        $spacer = new Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(Icons64x64_1::EmptyIcon);
        //$this->frame->addComponent($spacer);

        $this->label = new Label(40, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setText($point->name);
        $this->label->setScale(0.8);
        $this->frame->addComponent($this->label);


        $spacer = new Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);

        $this->label2 = new Label(90, 3);
        $this->label2->setAlign("left", "center");
        $this->label2->setTextSize(1);
        $this->label2->setText(implode(",", $point->points));
        $this->frame->addComponent($this->label2);

        $this->addComponent($this->frame);

        $this->button = new Button();
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
        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->destroyComponents();
        parent::destroy();
    }
}
