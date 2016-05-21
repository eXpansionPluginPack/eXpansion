<?php

namespace ManiaLivePlugins\eXpansion\ESportsManager\Gui\Controls;

class DirectoryItem extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    protected $bg;
    protected $label;
    protected $frame;

    /**
     *
     * @param \SplFileInfo $dir
     * @param type $sizeX
     */
    public function __construct(\SplFileInfo $dir, $controller, $compare, $sizeX)
    {
        $sizeY = 5;

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->addComponent($this->frame);

        $this->label = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button($sizeX, 5);
        $this->label->setAlign('center', 'center');
        $name = $dir->getFilename();
        if ($dir->getFilename() == "rules")
            $name = "[rules]";
        $this->label->setText($name);

        if ($dir->isDir()) {
            $this->label->setAction($this->createAction(array($controller, "setDirectory"), $dir->getRealPath()));
        }
        if ($dir->isFile()) {
            $this->label->setAction($this->createAction(array($controller, "setFile"), $dir->getRealPath()));
            //$this->label->colorize("0707");
        }
        if ($dir->getRealPath() == $compare) {
            $this->label->colorize("0e0a");
        }
        $this->label->setScale(0.8);
        $this->frame->addComponent($this->label);

        $this->setAlign("left");

        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY)
    {

        $this->frame->setSize($this->sizeX, $this->sizeY);
    }
}
