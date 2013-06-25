<?php

namespace ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Gui\Widgets;

use \ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Structures\Checkpoint;
use ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Gui\Controls\CheckpointElem;

class BestCpPanel extends \ManiaLive\Gui\Window {

    private $cps = array();

    /** @var \ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Structures\Checkpoint[]  */
    public static $bestTimes;
    private $frame;

    protected function onConstruct() {
        parent::onConstruct();
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Flow(220, 20));
        $this->frame->setSize(220, 20);
        $this->addComponent($this->frame);
    }

    function onDraw() {
        foreach ($this->cps as $cp) {
            $cp->destroy();
        }
        $this->cps = array();
        $this->frame->clearComponents();

        $x = 0;
        foreach (self::$bestTimes as $cp) {
            $this->cps[$x] = new CheckpointElem($cp);
            $this->frame->addComponent($this->cps[$x]);
            $x++;
            if ($x == 24)
                break;
        }
    }

    function destroy() {
        foreach ($this->cps as $cp) {
            $cp->destroy();
        }
        $this->cps = array();
        $this->clearComponents();

        parent::destroy();
    }

}

?>
