<?php

namespace ManiaLivePlugins\eXpansion\Widgets_MapSuggestion\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetButton;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Widget;

class MapSuggestionButton extends Widget
{

    /**
     * @var WidgetButton
     */
    public $btn_wish;

    protected function eXpOnBeginConstruct()
    {
        parent::eXpOnBeginConstruct();
        $line = new \ManiaLive\Gui\Controls\Frame(6, 0);
        $line->setAlign("center", "top");
        $line->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $this->btn_wish = new WidgetButton(10, 10);
        $this->btn_wish->setPositionZ(-1);
        $this->btn_wish->setText(array("Wish", "for", "Map"));
        $line->addComponent($this->btn_wish);

        $this->addComponent($line);

        $this->setName("Map Suggestion Button");
    }

    public function setActions($res)
    {
        $this->btn_wish->setAction($res);
    }

    public function destroy()
    {
        $this->destroyComponents();
        parent::destroy();
    }

}
