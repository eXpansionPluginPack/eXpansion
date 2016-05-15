<?php

namespace ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Gui\Controls\CheckpointElem;

class BestCpPanel extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{

    protected $cps = array();
    protected $maxCpIndex = 12;

    protected $frame;
    protected $storage;
    protected $timeScript;

    protected function eXpOnBeginConstruct()
    {
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Flow(220, 20));
        $this->frame->setSize(220, 20);
        $this->frame->setPosY(-2);
        $this->addComponent($this->frame);
        $this->setName("Best CheckPoints Widget");
        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->timeScript = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script('Widgets_BestCheckpoints/Gui/Scripts/BestCps');
        $this->timeScript->setParam("totalCp", $this->storage->currentMap->nbCheckpoints);
        $this->registerScript($this->timeScript);
    }

    public function onDraw()
    {

        $this->timeScript->setParam("totalCp", $this->storage->currentMap->nbCheckpoints);

        foreach ($this->cps as $cp) {
            $cp->destroy();
        }
        $this->cps = array();
        $this->frame->clearComponents();

        for ($x = 0; $x < 24 && $x < $this->storage->currentMap->nbCheckpoints; $x++) {
            $this->cps[$x] = new CheckpointElem($x);
            $this->frame->addComponent($this->cps[$x]);
        }

        $this->timeScript->setParam("maxCpIndex", $this->maxCpIndex);
        parent::onDraw();
    }

    public function destroy()
    {
        parent::destroy();
        foreach ($this->cps as $cp) {
            $cp->destroy();
        }
        $this->cps = array();
        $this->destroyComponents();

        parent::destroy();
    }

}