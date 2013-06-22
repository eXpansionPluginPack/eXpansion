<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Times\Gui\Widgets;

class TimePanel extends \ManiaLive\Gui\Window {

    private $checkpoint;
    private $time;
    private $bestRun = array();
    private $currentRun = array();
    private $lastFinish = -1;
    private $counter = 1;

    protected function onConstruct() {
        parent::onConstruct();

        $this->time = new \ManiaLib\Gui\Elements\Label(20, 4);
        $this->time->setPosX(7);
        $this->time->setAlign("left", "center");
        $this->time->setStyle("TextTitle2");
        $this->addComponent($this->time);

        $this->checkpoint = new \ManiaLib\Gui\Elements\Label(6, 4);
        $this->checkpoint->setPosX(0);
        $this->checkpoint->setTextColor("fff");
        $this->checkpoint->setAlign("left", "center");
        $this->addComponent($this->checkpoint);

        $this->setAlign("center", "top");
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
    }

    function onShow() {
        
    }

    public function onCheckpoint($time, $cpIndex) {
        $this->currentRun[$cpIndex] = $time;
        $this->checkpoint->setText($this->counter . ".." . ($cpIndex+1));
        $this->time->setTextColor('fffa');
        $this->time->setText(\ManiaLive\Utilities\Time::fromTM($time, false));

        if (isset($this->bestRun[$cpIndex])) {
            $this->time->setText(\ManiaLive\Utilities\Time::fromTM($time - $this->bestRun[$cpIndex], true));
            $this->time->setTextColor('a00a');
            if ($this->bestRun[$cpIndex] > $time)
                $this->time->setTextColor('00aa');
        }
    }

    public function onFinish($time) {
        if ($time < $this->lastFinish || $this->lastFinish == -1) {
            $this->lastFinish = $time;
            $this->bestRun = $this->currentRun;
            $this->counter++;
        }
    }

    public function onStart() {
        $this->currentRun = array();
    }

    function destroy() {
        $this->clearComponents();
        parent::destroy();
    }

}

?>
