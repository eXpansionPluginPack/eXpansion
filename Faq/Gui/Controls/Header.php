<?php

namespace ManiaLivePlugins\eXpansion\Faq\Gui\Controls;

/**
 * Description of Header
 *
 * @author Reaby
 */
class Header extends FaqControl
{

    public function __construct($text, $level = 1)
    {
        parent::__construct($text);
        $this->label->setStyle("TextRaceMessageBig");
        $this->label->setTextSize(6 - $level);
        $this->label->setTextColor("fff");

        $this->setSizeY(8 - $level);
        $this->setAlign("left", "top");
    }
}
