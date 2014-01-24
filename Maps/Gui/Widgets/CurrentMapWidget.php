<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Widgets;

class CurrentMapWidget extends \ManiaLivePlugins\eXpansion\Gui\Windows\Widget {

    protected $bg;
    protected $authorTime, $logo;
    private $frame;

    protected function onConstruct() {
        parent::onConstruct();
        $bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(54, 13);        
        $bg->setPosition(-44, 3);
        $this->addComponent($bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame(-44,0);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column());
        $this->addComponent($this->frame);
        
        $icon = new \ManiaLib\Gui\Elements\Quad(4.5, 4.5);
        $icon->setStyle("UIConstructionSimple_Buttons");
        $icon->setSubStyle("AuthorTime");
        $icon->setAlign("left", "center2");
        $icon->setPosition(5.2, -1);
        $this->addComponent($icon);

        $label = new \ManiaLib\Gui\Elements\Label();
        $label->setId("mapName");  
        $label->setText("none");
        $label->setScriptEvents();
        $this->frame->addComponent($label);
        
        $label = new \ManiaLib\Gui\Elements\Label();
        $label->setId("authorName");        
        $label->setText("none");
        $label->setScriptEvents();
        $this->frame->addComponent($label);
        
        $label = new \ManiaLib\Gui\Elements\Label();
        $label->setId("authorTime");        
        $label->setText("none");
        $label->setScriptEvents();
        $this->frame->addComponent($label);
        
        $label = new \ManiaLib\Gui\Elements\Quad(5,5);
        $label->setId("authorZone");             
        $label->setScriptEvents();
        $this->frame->addComponent($label);
                
        $script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Maps\Gui\Scripts_CurrentMap");        
        $this->registerScript($script);
        
        $this->setName("Current Map Widget");
    }

    function setMap(\Maniaplanet\DedicatedServer\Structures\Map $map) {
        //$this->authorTime->setText(\ManiaLive\Utilities\Time::fromTM($map->authorTime));
    }

    function destroy() {
        parent::destroy();
    }

}

?>
