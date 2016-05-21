<?php
namespace ManiaLivePlugins\eXpansion\Faq\Gui\Controls;
/**
 * Description of Header
 *
 * @author Reaby
 */
class Line extends FaqControl
{

    public function __construct($text)
    {
        parent::__construct($text);
        $this->label->setTextColor("fff");
    }

}

?>
