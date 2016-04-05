<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Controls;

class ScriptSetting extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    private $bg;
    private $label;
    private $inputbox;
    private $frame;
    public $checkBox = null;
    public $settingName;
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

        $this->label = new \ManiaLib\Gui\Elements\Label(120, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setText($settingName);
        $this->label->setScale(0.8);
        $this->frame->addComponent($this->label);


        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);

        if (is_bool($value) === true) {
            $this->checkBox = new \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox(4, 4);
            $this->checkBox->setStatus($value);

            $this->frame->addComponent($this->checkBox);
        } else {
            $this->inputbox = new \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox($settingName, 20);
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
