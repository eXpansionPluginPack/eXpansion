<?php

namespace ManiaLivePlugins\eXpansion\Communication\Gui\Controls;

/**
 *
 * @author reaby
 */
class Tab extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    public function __construct($idx)
    {
        $sizeX = 22;
        $sizeY = 4;

        $background = new \ManiaLib\Gui\Elements\Quad(22, 5);
        $background->setStyle("Bgs1");
        $background->setSubStyle("BgCard3");
        $background->setId("tabBg_" . $idx);
        $background->setScriptEvents();
        $this->addComponent($background);

        $label = new \ManiaLib\Gui\Elements\Label(20, 5);
        $label->setPosition(1, -.5);
        $label->setText("Tab " . $idx);
        $label->setId("tablabel_" . $idx);
        $label->setTextColor("fff");
        $label->setStyle("TextCardScores2");
        //$label->setScriptEvents();
        $label->setTextSize(1);
        $this->addComponent($label);

        $closeButton = new \ManiaLib\Gui\Elements\Quad(4, 4);
        $closeButton->setStyle("Icons128x32_1");
        $closeButton->setSubStyle("Close");
        $closeButton->setPosX(18);
        $closeButton->setScriptEvents();
        $closeButton->setId("closeButton_" . $idx);
        $this->addComponent($closeButton);


        $this->setSize($sizeX, $sizeY);
    }

    public function destroy()
    {
        $this->destroyComponents();
        parent::destroy();
    }

}
