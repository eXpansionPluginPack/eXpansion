<?php

namespace ManiaLivePlugins\eXpansion\MapRatings\Gui\Controls;

class RateButton2 extends \ManiaLivePlugins\eXpansion\Gui\Control
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
    function __construct($number)
    {
        $sizeX = 18;
        $sizeY = 6;
        $this->setAlign("left");

        $this->quad = new \ManiaLib\Gui\Elements\Quad(19, 8);
        $this->quad->setAlign('left', 'center2');
        $this->quad->setBgcolor("0000");
        $this->quad->setAttribute("class", "rateButton");
        $this->quad->setId("button_" . $number);
        $this->quad->setScriptEvents();
        $this->quad->setPosition(-2, -.75);
        $this->addComponent($this->quad);

        $thumb = new \ManiaLib\Gui\Elements\Quad(10, 10);
        $thumb->setId("rate_" . $number);
        $thumb->setAttribute("class", "rateButton");
        $thumb->setAlign("center", "center");
        $thumb->setStyle("Icons64x64_1");
        $thumb->setPosX(1);
        $thumb->setSubStyle("StateSuggested");

        $label = new \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel(16, 6);
        $label->setPosition(6, -1);
        $label->setAlign("left", "center");
        $label->setId("label_" . $number);
        switch ($number) {
            case 0:
                // $thumb->setColorize("f00");
                $thumb->setAttribute("rot", "180");
                $label->setText(exp_getMessage("No"));
                break;
            case 5;
                // $thumb->setColorize("0f0");
                $label->setText(exp_getMessage("Yes"));
                break;
        }


        $this->addComponent($thumb);
        $this->addComponent($label);

        $this->setSize($sizeX, $sizeY);
    }

}

?>