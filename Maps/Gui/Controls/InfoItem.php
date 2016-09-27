<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Controls;

class InfoItem extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    protected $bg;

    protected $label;

    protected $frame;

    public function __construct($indexNumber, $text, $sizeX)
    {
        $sizeY = 6;

        $this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround($indexNumber, $sizeX, $sizeY);
        $this->addComponent($this->bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());


        $this->label = new \ManiaLib\Gui\Elements\Label(90, 6);
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


