<?php

namespace ManiaLivePlugins\eXpansion\Widgets_ReadyState\Gui\Widgets;

use ManiaLib\Gui\Elements\Label;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Widget;

/**
 * Description of Countdown
 *
 * @author Petri
 */
class ReadyWidget extends Widget
{
    /** @var  Label */
    protected $label;

    public function eXpOnBeginConstruct()
    {
        $this->setName("ReadyState");
        $this->label = new Label(60, 6);
        $this->addComponent($this->label);
    }


    public function setText($text)
    {
        $this->label->setText($text);
    }

}