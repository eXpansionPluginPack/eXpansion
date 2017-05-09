<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Controls;

use ManiaLib\Gui\Elements\Icons64x64_1;
use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Control;
use ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;

class ScriptSetting extends Control
{
    /** @var ListBackGround */
    private $bg;
    /** @var Label */
    private $label;
    /** @var Inputbox */
    private $inputbox;
    /** @var Frame */
    private $frame;
    /** @var Checkbox|null */
    public $checkBox = null;
    /** @var string */
    public $settingName;
    /** @var null|string */
    public $type = null;

    /**
     *
     * @param int $indexNumber
     * @param string $settingName
     * @param mixed $value
     * @param int $sizeX
     */
    public function __construct($indexNumber, $settingName, $value, $sizeX)
    {
        $sizeY = 6;
        $this->settingName = $settingName;
        $this->type = gettype($value);

        $this->bg = new ListBackGround($indexNumber, $sizeX, $sizeY);
        $this->addComponent($this->bg);

        $this->frame = new Frame();
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
        $this->label->setText($settingName);
        $this->label->setScale(0.8);
        $this->frame->addComponent($this->label);


        $spacer = new Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);

        if (is_bool($value) === true) {
            $this->checkBox = new Checkbox(4, 4);
            $this->checkBox->setStatus($value);

            $this->frame->addComponent($this->checkBox);
        } else {
            $this->inputbox = new Inputbox($settingName, 20);
            $this->inputbox->setText($value);
            $this->frame->addComponent($this->inputbox);
        }
        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY)
    {
        $this->bg->setSize($this->sizeX, $this->sizeY);
        $this->bg->setPosX(-2);
        $this->frame->setSize($this->sizeX, $this->sizeY);
    }


    public function destroy()
    {
        parent::destroy();
    }
}
