<?php

namespace ManiaLivePlugins\eXpansion\Widgets_ReadyState\Gui\Widgets;

use ManiaLib\Gui\Elements\Label;

/**
 * Description of Countdown
 *
 * @author Petri
 */
class ReadyWidget extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{
    /** @var  Label */
    protected $label;

    protected function eXpOnBeginConstruct()
    {

        $this->label = new Label(60, 6);
        $this->addComponent($this->label);
        $this->setName("ReadyState");

    }


    public function setText($text)
    {
        $this->label->setText($text);
    }

}