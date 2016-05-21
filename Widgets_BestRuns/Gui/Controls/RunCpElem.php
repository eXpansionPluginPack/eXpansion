<?php

namespace ManiaLivePlugins\eXpansion\Widgets_BestRuns\Gui\Controls;

class RunCpElem extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    protected $bg;
    protected $time;

    public function __construct($index, $time)
    {
        $sizeX = 15;
        $sizeY = 5;

        /*$this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->bg->setStyle("BgsPlayerCard");
        $this->bg->setSubStyle("BgPlayerCardSmall");
        $this->bg->setAlign('center', 'center');
        $this->addComponent($this->bg); */

        $this->label = new \ManiaLib\Gui\Elements\Label($sizeX, $sizeY);
        $this->label->setAlign('center', 'center');
        $this->label->setPosX(0);
        $this->label->setTextSize(1);
        if ($time != 0)
            $this->label->setText('$ff0' . ($index + 1) . ' $fff' . \ManiaLive\Utilities\Time::fromTM($time));

        $this->addComponent($this->label);

        $this->setSize($sizeX + 5, $sizeY);
    }
    
}
