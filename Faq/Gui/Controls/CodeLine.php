<?php

namespace ManiaLivePlugins\eXpansion\Faq\Gui\Controls;

use ManiaLib\Gui\Elements\Quad;

/**
 * Description of Header
 *
 * @author Reaby
 */
class CodeLine extends FaqControl
{

    public $background;

    public function __construct($text)
    {
        $this->background = new Quad();
        $this->background->setSize(240, 8);
        $this->background->setAlign("left", "center");
        $this->background->setBgcolor("3af8");
        $this->addComponent($this->background);
        $text = str_replace("```", "", $text);

        parent::__construct($text);
        $this->setSizeY(8);
        $this->label->setSizeY(8);
        $this->label->setStyle("StyleTextScriptEditor");
        $this->label->setTextSize(1.5);
        $this->label->setTextColor("ff0");
    }
}
