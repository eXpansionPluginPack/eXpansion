<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Times\Gui\Widgets;

/**
 * Description of CheckpointWidget
 *
 * @author Reaby
 */
class CheckpointWidget extends \ManiaLive\Gui\Window {

    protected $position, $time, $frame;

    protected function onConstruct() {
        parent::onConstruct();

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->frame->setAlign("center");
        $this->addComponent($this->frame);


        $this->position = new \ManiaLib\Gui\Elements\Label(12, 6);
        $this->position->setTextColor("ffff");
        $this->position->setAlign("left", "center");
        $this->position->setTextSize(4);
        $this->frame->addComponent($this->position);

        $this->time = new \ManiaLib\Gui\Elements\Label(26, 6);
        $this->time->setStyle("TextRaceChrono");
        $this->time->setTextColor("ffff");
        $this->time->setScale(0.75);
        $this->time->setAlign("left", "center");
        $this->frame->addComponent($this->time);
        $this->setAlign("center");
    }

    public function setData($pos, $time) {
        $this->position->setText($pos);
        $this->time->setText($time);
    }

}
