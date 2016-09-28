<?php

namespace ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Controls;

class MxInfo extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    protected $bg;
    protected $label;
    protected $time;
    protected $frame;

    public function __construct($indexNumber, $message, $sizeX)
    {
        $sizeY = 5.5;


        $this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->bg->setAlign('left', 'center');
        if ($indexNumber % 2 == 0) {
            $this->bg->setBgcolor('aaa4');
        } else {
            $this->bg->setBgcolor('7774');
        }
        $this->addComponent($this->bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);

        $this->addComponent($this->frame);

        $info = new \ManiaLib\Gui\Elements\Label(120, 4);
        $info->setAlign('left', 'center');
        $info->setText('$fff' . $message);
        $info->setStyle("TextCardSmallScores2");
        $info->setScriptEvents(true);
        $this->frame->addComponent($info);


        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
    }

    protected function onResize($oldX, $oldY)
    {
        $this->bg->setSize($this->sizeX, $this->sizeY);
        $this->bg->setPosX(-2);
        $this->frame->setSize($this->sizeX, $this->sizeY + 1);
        //  $this->button->setPosx($this->sizeX - $this->button->sizeX);
    }

    public function onIsRemoved(\ManiaLive\Gui\Container $target)
    {
        parent::onIsRemoved($target);
        $this->erase();
    }

    // override destroy method not to destroy its contents on manialive 3.1
    public function destroy()
    {

    }

    /**
     * custom function to destroy contents when needed.
     */
    public function erase()
    {
        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->destroyComponents();
        parent::destroy();
    }
}
