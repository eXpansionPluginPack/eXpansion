<?php

namespace ManiaLivePlugins\eXpansion\Widgets_TM_Obstacle\Gui\Widgets;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Layouts\Column;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Data\Storage;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Elements\Gauge;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Widget;

class CpProgress extends Widget
{

    protected $frame;

    /** @var Storage; */
    protected $storage;

    protected function eXpOnBeginConstruct()
    {
        $this->setName("Obstacle progress Widget");
        $this->storage = Storage::getInstance();

        $this->frame = new Frame();
        $this->frame->setLayout(new Column());
        $this->addComponent($this->frame);

        for ($x = 0; $x < 10; $x++) {
            $line = new Frame();
            $line->setLayout(new Line());
            $line->setSize(70, 6);
            $line->setAlign("left", "top");


            $label = new Label(30, 9);
            $label->setPosX(30);
            $label->setAlign("right", "top");
            $label->setId("player_" . $x);
            $label->setText("player_" . $x);
            $line->addComponent($label);

            $gauge = new Gauge(30, 9);
            $gauge->setPosY(-2);
            $gauge->setAlign("left", "center2");
            $gauge->setId("gauge_" . $x);
            $gauge->setColorize("2f2");
            $line->addComponent($gauge);

            $label = new Label(10, 9);
            $label->setAlign("left", "top");
            $label->setId("cp_" . $x);
            $label->setText("1");
            $line->addComponent($label);

            $this->frame->addComponent($line);
        }

        $script = new Script("Widgets_TM_Obstacle\Gui\Scripts_Infos");
        $script->setParam("playerCount", $x);
        $script->setParam("totalCp", $this->storage->currentMap->nbCheckpoints);
        $script->setParam("serverLogin", $this->storage->serverLogin);
        $this->registerScript($script);
    }

    public function destroy()
    {
        $this->destroyComponents();
        $this->storage = null;
        parent::destroy();
    }

}
