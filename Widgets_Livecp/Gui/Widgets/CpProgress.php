<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Livecp\Gui\Widgets;

use ManiaLib\Gui\Layouts\Column;
use ManiaLive\Data\Storage;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Widget;
use ManiaLivePlugins\eXpansion\Widgets_Livecp\Gui\Controls\CpItem;

class CpProgress extends Widget
{
    /** @var  Frame */
    protected $frame;

    /** @var Storage; */
    protected $storage;

    protected function eXpOnBeginConstruct()
    {
        $this->setName("CpLive Widget");
        $this->storage = Storage::getInstance();

        $this->frame = new Frame();
        $this->frame->setLayout(new Column());
        $this->addComponent($this->frame);

    }

    public function setData($playerData)
    {
        $cpData = array();
        foreach ($playerData as $login => $time) {
            $cpData[$login] = count($time);
        }

        arsort($cpData, SORT_NUMERIC);
        $x = 0;

        foreach ($cpData as $login => $index) {
            if ($x >= 16) {
                break;
            }

            $element = new CpItem($x, $this->storage->getPlayerObject($login), $playerData[$login], $this->storage->currentMap->nbCheckpoints);
            $this->frame->addComponent($element);

            $x++;
        }


    }

    public function destroy()
    {
        $this->destroyComponents();
        $this->storage = null;
        parent::destroy();
    }

}
