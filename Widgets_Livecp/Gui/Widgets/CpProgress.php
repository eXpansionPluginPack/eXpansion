<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Livecp\Gui\Widgets;

use ManiaLib\Gui\Layouts\Column;
use ManiaLive\Data\Storage;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround;
use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetTitle;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Widget;
use ManiaLivePlugins\eXpansion\Widgets_Livecp\Gui\Controls\CpItem;
use ManiaLivePlugins\eXpansion\Widgets_Livecp\Structures\CpInfo;

class CpProgress extends Widget
{
    /** @var  Frame */
    protected $frame;
    protected $wframe;

    protected $bg;
    protected $title;
    protected $trayWidget;

    /** @var Storage; */
    protected $storage;

    protected function eXpOnBeginConstruct()
    {
        $x = 44;
        $y = 68;
        $this->setName("CpLive Widget");
        $this->storage = Storage::getInstance();

        $this->wframe = new Frame();
        $this->wframe->setAlign("left", "top");
        $this->wframe->setId("Frame");
        $this->wframe->setScriptEvents(true);
        $this->addComponent($this->wframe);

        $this->bg = new WidgetBackGround($x, $y);
        $this->wframe->addComponent($this->bg);

        $this->title = new WidgetTitle($x, $y);
        $this->title->setText("LiveCP      - Total CP: " . $this->storage->currentMap->nbCheckpoints);
        $this->title->setId("minimizeButton");
        $this->title->setScriptEvents();
        $this->title->setDirection("right");
        $this->wframe->addComponent($this->title);

        $this->frame = new Frame();
        $this->frame->setLayout(new Column());
        $this->wframe->addComponent($this->frame);

        $this->trayWidget = new Script("Gui/Scripts/NewTray");
        $this->registerScript($this->trayWidget);

    }

    protected function eXpOnEndConstruct()
    {
        $this->setSize(54, 90);
    }

    public function setData($playerData)
    {
        uasort($playerData, array($this, 'compare'));

        $x = 0;
        foreach ($playerData as $login => $data) {
            if ($x >= 16) {
                break;
            }

            $element = new CpItem(
                $x,
                $this->storage->getPlayerObject($login),
                $data,
                $this->storage->currentMap->nbCheckpoints
            );
            $this->frame->addComponent($element);
            $x++;
        }


    }

    public function compare($a, $b)
    {
        if ($a->cpIndex > $b->cpIndex) {
            return -1;
        } elseif ($a->cpIndex < $b->cpIndex) {
            return 1;
        } elseif ($a->time < $b->time) {
            return -1;
        } elseif ($a->time > $b->time) {
            return 1;
        }

    }

    public function destroy()
    {
        $this->destroyComponents();
        $this->storage = null;
        parent::destroy();
    }

}
