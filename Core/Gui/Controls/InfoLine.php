<?php

namespace ManiaLivePlugins\eXpansion\Core\Gui\Controls;

/**
 * Description of InfoLine
 *
 * @author Reaby
 */
class InfoLine extends \ManiaLive\Gui\Control {

    /** @var ManiaLib\Gui\Elements\Label */
    protected $label;

    public function __construct($text) {
        $this->setSize(240, 4);
        $this->setAlign("left");
        $this->label = new \ManiaLib\Gui\Elements\Label(240, 5);
        $this->label->setAlign("left", "center");
        $this->label->setStyle("TextCardMedium");
        $this->label->setText($text);
        $this->label->setTextSize(1);
        $this->addComponent($this->label);
    }

    public function destroy() {
        parent::destroy();
    }

}
