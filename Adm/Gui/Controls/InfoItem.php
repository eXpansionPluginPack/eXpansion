<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Controls;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;

class InfoItem extends \ManiaLivePlugins\eXpansion\Gui\Control
{
    /** @var ListBackGround */
    private $bg;
    /** @var Label */
    private $label;
    /** @var Frame */
    private $frame;

    /**
     * InfoItem constructor.
     * @param $indexNumber
     * @param $text
     * @param $sizeX
     */
    public function __construct($indexNumber, $text, $sizeX)
    {
        $sizeY = 6;

        $this->bg = new ListBackGround($indexNumber, $sizeX, $sizeY);
        $this->addComponent($this->bg);

        $this->frame = new Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new Line());


        $this->label = new Label(90, 6);
        $this->label->setAlign('left', 'center');
        $this->label->setText($text);
        $this->label->setTextSize(1);
        $this->label->setStyle("TextCardSmallScores2");
        $this->label->setTextColor("fff");
        $this->frame->addComponent($this->label);

        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY)
    {
        $this->bg->setSize($this->sizeX + 2, $this->sizeY);
        $this->bg->setPosX(0);

        $this->frame->setPosX(2);
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
        $this->bg->destroy();
        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->destroyComponents();

        parent::destroy();
    }
}
