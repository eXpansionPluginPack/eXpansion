<?php

namespace ManiaLivePlugins\eXpansion\MapRatings\Gui\Controls;

class RateButton extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    protected $label;

    protected $frame;

    protected $quad;

    /**
     * Button
     *
     * @param int $sizeX = 24
     * @param intt $sizeY = 6
     */
    public function __construct($number)
    {
        $sizeX = 22;
        $sizeY = 8;
        $this->setAlign("left");

        $this->label = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();

        $this->label->$this->label->setAlign('center', 'center');
        $this->label->setText("+" . $number);
        $this->label->setId("rate_" . $number);
        $this->label->setAttribute("class", "rateButton");

        $this->addComponent($this->label);

        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY)
    {
        $this->label->setPosX(($this->sizeX - 2) / 2);
        $this->label->setPosZ($this->posZ);

        parent::onResize($oldX, $oldY);
    }
}
