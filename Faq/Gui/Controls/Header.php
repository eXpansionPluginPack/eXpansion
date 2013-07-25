<?php

namespace ManiaLivePlugins\eXpansion\Faq\Gui\Controls;

/**
 * Description of Header
 *
 * @author Reaby
 */
class Header extends FaqControl {

    public function __construct($text) {
        $text = str_replace("#", "", $text);
        parent::__construct($text);
        $this->label->setStyle("TextRaceMessageBig");
        $this->label->setTextSize(3);
        $this->label->setTextColor("0af");
        $this->setSizeY(7);
        $this->setAlign("left", "top");
        
       }

}

?>
