<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLive\Gui\Controls\Frame;

class FrameClipable extends Frame
{

    protected $clip = true;
    protected $clipSizeX = 100;
    protected $clipSizeY = 90;
    protected $clipPosX = 0;
    protected $clipPosY = 0;


    public function __construct($sizeX = 100, $sizeY = 90)
    {
        parent::__construct($sizeX, $sizeY, null);
        $this->clipSizeX = $sizeX;
        $this->clipSizeY = $sizeY;
        $this->setAttributes();
    }


    public function setClipSizeX($x)
    {
        $this->clipSizeX = $x;
        $this->setAttributes();
    }

    public function setClipSizeY($y)
    {
        $this->clipSizeX = $y;
        $this->setAttributes();
    }

    public function setClipSize($x, $y)
    {
        $this->clipSizeX = $x;
        $this->clipSizeY = $y;
        $this->setAttributes();
    }


    private function setAttributes()
    {
        $this->setAttribute("clip", $this->clip ? "True" : "False");
        $this->setAttribute("clipposn", $this->clipPosX . " " . $this->clipPosY);
        $this->setAttribute("clipsizen", $this->clipSizeX . " " . $this->clipSizeY);
    }

}
