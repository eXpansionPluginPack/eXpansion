<?php

namespace ManiaLivePlugins\eXpansion\Widgets_CheckpointProgress\Gui\Widgets;

use ManiaLive\Data\Storage;
use ManiaLivePlugins\eXpansion\Core\Core;
use ManiaLivePlugins\eXpansion\Gui\Elements\Gauge;
use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Widget;

class CpProgress extends Widget
{

    protected $clockBg;
    protected $gauge1;
    protected $script;

    /** @var Storage; */
    protected $storage;

    protected function eXpOnBeginConstruct()
    {
        $this->setAlign("left", "top");

        $bg = new WidgetBackGround(60, 6);
        $bg->setAction(Core::$action_serverInfo);
        // $this->addComponent($bg);
        $this->gauge1 = new Gauge(160, 7);
        $this->gauge1->setAlign("left", "top");
        $this->gauge1->setId("totalProgress");
        $this->gauge1->setColorize("2f2");
        $this->addComponent($this->gauge1);

        $this->gauge2 = new Gauge(160, 7);
        $this->gauge2->setPosY(-3);
        $this->gauge2->setStyle("EnergyBar");
        $this->gauge2->setAlign("left", "top");
        $this->gauge2->setId("myProgress");
        $this->gauge2->setColorize("2af");
        $this->addComponent($this->gauge2);

        $this->storage = Storage::getInstance();
        $script = new Script("Widgets_CheckpointProgress\Gui\Scripts_Infos");
        $script->setParam("totalCp", $this->storage->currentMap->nbCheckpoints);
        $this->registerScript($script);
        $this->setName("Checkpoint progress Widget");
    }

    public function destroy()
    {
        $this->destroyComponents();
        $this->storage = null;
        parent::destroy();
    }
}
