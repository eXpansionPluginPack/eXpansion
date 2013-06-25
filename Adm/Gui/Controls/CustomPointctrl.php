<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Controls;

class CustomPointctrl extends \ManiaLive\Gui\Control {

    private $bg;
    private $label;
    private $label2;
    private $frame;
    private $action;

    function __construct($indexNumber, $point, $plugin, $login, $sizeX) {
        $sizeY = 4;

        $this->action = $this->createAction(array($plugin, "setPoints"), $point->points);
        $this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround($indexNumber, $sizeX, $sizeY);
        $this->addComponent($this->bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());


        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setAlign("center", "center2");
        $spacer->setStyle("Icons128x128_1");
        $spacer->setSubStyle("Challenge");
        $this->frame->addComponent($spacer);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        //$this->frame->addComponent($spacer);

        $this->label = new \ManiaLib\Gui\Elements\Label(40, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setText($point->name);
        $this->label->setScale(0.8);
        $this->frame->addComponent($this->label);


        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);

        $this->label2 = new \ManiaLib\Gui\Elements\Label(100, 3);
        $this->label2->setAlign("left", "center");
        $this->label2->setTextSize(1);
        $this->label2->setText(implode(",", $point->points));
        $this->frame->addComponent($this->label2);

        $this->addComponent($this->frame);

        $this->button = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
        $this->button->setText(__("Load", $login));
        $this->button->setScale(0.5);
        $this->button->setAction($this->action);
        $this->frame->addComponent($this->button);

        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY) {
        $this->bg->setSize($this->sizeX, $this->sizeY);
        $this->bg->setPosX(-2);
        $this->frame->setSize($this->sizeX, $this->sizeY);
    }

    function onDraw() {
        
    }

    function destroy() {
        $this->button->destroy();
        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->clearComponents();
        parent::destroy();
    }

}
?>

