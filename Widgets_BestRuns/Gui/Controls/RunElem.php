<?php

namespace ManiaLivePlugins\eXpansion\Widgets_BestRuns\Gui\Controls;

class RunElem extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    protected $container;
    protected $frame;
    protected $player;
    protected $cps = array();

    public function __construct(\ManiaLivePlugins\eXpansion\Widgets_BestRuns\Structures\Run $run)
    {
        $x = 0;
        $this->setSize(220, 6);
        $this->container = new \ManiaLive\Gui\Controls\Frame();
        $this->container->setLayout(new \ManiaLib\Gui\Layouts\Line(220, 30));
        $this->addComponent($this->container);

        $this->player = new \ManiaLivePlugins\eXpansion\Widgets_BestRuns\Gui\Controls\RunPlayerElem($run->player);
        $this->container->addComponent($this->player);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Flow(170, 6));
        $this->frame->setSize(170, 30);
        $this->container->addComponent($this->frame);

        $i = 0;
        foreach ($run->checkpoints as $time) {
            $this->cps[$i] = new \ManiaLivePlugins\eXpansion\Widgets_BestRuns\Gui\Controls\RunCpElem($i, $time);
            $this->frame->addComponent($this->cps[$i]);
            $i++;
            if ($i == 26)
                break;
        }
    }

    public function destroy()
    {
        foreach ($this->cps as $item) {
            $item->destroy();
        }
        $this->cps = array();
        $this->destroyComponents();
        parent::destroy();
    }

}

