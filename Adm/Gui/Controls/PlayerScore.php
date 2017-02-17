<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Controls;

use ManiaLib\Gui\Elements\Icons64x64_1;
use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Control;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;

class PlayerScore extends Control
{
    /** @var ListBackGround */
    private $bg;
    /** @var Label */
    private $label;
    /** @var Inputbox */
    private $inputbox;
    /** @var Frame */
    private $frame;

    /**
     *
     * @param int $indexNumber
     * @param mixed $player
     * @param int $sizeX
     */
    public function __construct($indexNumber, $player, $sizeX)
    {
        $sizeY = 6;
        $this->bg = new ListBackGround($indexNumber, $sizeX, $sizeY);
        $this->addComponent($this->bg);

        $this->frame = new Frame(4, 0);
        $this->frame->setSize($sizeX, $sizeY);
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

        $this->label = new Label(120, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setText($player->nickName);
        $this->label->setScale(0.8);
        $this->frame->addComponent($this->label);


        $spacer = new Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);

        $this->inputbox = new Inputbox($player->playerId, 20);
        $this->inputbox->setText($player->score);
        $this->frame->addComponent($this->inputbox);

        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
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
        $this->inputbox->destroy();
        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->destroyComponents();
        parent::destroy();
    }
}
