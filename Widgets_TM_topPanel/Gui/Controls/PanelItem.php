<?php

namespace ManiaLivePlugins\eXpansion\Widgets_TM_topPanel\Gui\Controls;

class PanelItem extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    public $quad, $lbl_title, $lbl_value;

    public function __construct($title, $value, $sizeX = 20, $StyleorUrl = null, $iconSubStyle = null)
    {
        $this->quad = new \ManiaLib\Gui\Elements\Quad(8, 8);
        //$this->quad->setColorize("fff");
        $this->quad->setPosY(0.5);
        $this->quad->setStyle($StyleorUrl);
        $this->quad->setSubStyle($iconSubStyle);
        //	$this->addComponent($this->quad);

        $this->lbl_title = new \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel($sizeX, 4);
        $this->lbl_title->setText($title);
        $this->lbl_title->setPosition(0, -4);
        $this->lbl_title->setStyle("TextCardSmallScores2");
        $this->addComponent($this->lbl_title);


        $this->lbl_value = new \ManiaLib\Gui\Elements\Label($sizeX, 4);
        $this->lbl_value->setText($value);
        $this->lbl_value->setPosition(0, 0);
        $this->lbl_value->setStyle("TextCardSmallScores2");

        $this->addComponent($this->lbl_value);

        $this->setSize($sizeX + 6, 8);
        $this->setScale(0.9);
    }

    public function setId($id)
    {
        $this->lbl_value->setId($id);
    }

    public function setIdTitle($id)
    {
        $this->lbl_title->setId($id);
    }

    public function setQuadid($id)
    {
        $this->quad->setId($id);
    }

}
