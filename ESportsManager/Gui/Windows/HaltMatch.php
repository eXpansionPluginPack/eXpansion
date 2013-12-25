<?php

namespace ManiaLivePlugins\eXpansion\ESportsManager\Gui\Windows;

/**
 * Description of Stop
 *
 * @author Reaby
 */
class HaltMatch extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    protected $label_halt, $label_reason, $frame;

    protected function onConstruct() {
        parent::onConstruct();
        $login = $this->getRecipient();
        $this->setTitle(__("Stop Match", $login));
        $this->frame = new \ManiaLive\Gui\Controls\Frame(2, -4);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column());
        $this->addComponent($this->frame);

        $this->label_halt = new \ManiaLib\Gui\Elements\Label(60,15);
        $this->label_halt->setAlign("center", "top");
        $this->label_halt->setStyle(\ManiaLib\Gui\Elements\Format::TextRaceMessageBig);
        $this->label_halt->setText(__("Stop Match", $login));
        $this->label_halt->setTextColor("f00");
        $this->label_halt->setTextEmboss();
        $this->frame->addComponent($this->label_halt);

        $this->label_reason = new \ManiaLib\Gui\Elements\Label(60,5);
        $this->label_reason->setAlign("center", "top");
        $this->label_reason->setTextColor("000");
        $this->frame->addComponent($this->label_reason);

        $this->setAlign("center");
    }

    function onResize($oldX, $oldY) {
        $this->label_halt->setPosX($this->sizeX / 2);
        $this->label_reason->setPosX($this->sizeX / 2);

        parent::onResize($oldX, $oldY);
    }

    function setReason($reason = "Admin has requested to stop the match!") {
        $this->label_reason->setText($reason);
    }

}
