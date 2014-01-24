<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Widgets;

class CurrentMapWidget extends \ManiaLivePlugins\eXpansion\Gui\Windows\Widget {

    protected $bg;
    protected $authorTime, $logo;
    private $frame, $icons;

    protected function onConstruct() {
        parent::onConstruct();
        $this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(60, 15);
        $this->bg->setPosition(-42, 0);
        $this->addComponent($this->bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame(-34, 4);
        $column = new \ManiaLib\Gui\Layouts\Column();        
        $this->frame->setLayout($column);        
        $this->addComponent($this->frame);

        $this->icons = new \ManiaLive\Gui\Controls\Frame(-35, 4);
        $column = new \ManiaLib\Gui\Layouts\Column(10,4);        
        $this->icons->setLayout($column);
        $this->addComponent($this->icons);
        
        $style = 'TextCardRaceRank';
        $scale = '1';
        $size = 1;
        
        $label = new \ManiaLib\Gui\Elements\Label(40, 4);
        $label->setId("mapName");
        $label->setText("none");
        $label->setStyle($style);
        $label->setScale($scale);
        $label->setTextSize($size);        
        $label->setAlign("left", "center");
        $label->setTextEmboss();
        $this->frame->addComponent($label);
        
        $icon = new \ManiaLib\Gui\Elements\Quad(4, 4);
        $icon->setStyle("UIConstructionSimple_Buttons");
        $icon->setSubStyle("Challenge");               
        $icon->setAlign("right", "center");
        $this->icons->addComponent($icon);
        
        
        
        $label = new \ManiaLib\Gui\Elements\Label(40, 4);        
        $label->setId("authorName");
        $label->setText("none");
        $label->setStyle($style);
        $label->setScale($scale);
        $label->setTextSize($size);
        $label->setTextEmboss();        
        $label->setAlign("left", "center");
        $this->frame->addComponent($label);
        
        $label = new \ManiaLib\Gui\Elements\Quad(4, 4);
        $label->setId("authorZone");        
        $label->setImage("http://reaby.kapsi.fi/ml/flags/Other%20Countries.dds", true);
        $label->setAlign("right", "center");
        $this->icons->addComponent($label);
        
        $label = new \ManiaLib\Gui\Elements\Label(40, 4);
        $label->setId("authorTime");
        $label->setText("none");
        $label->setStyle($style);
        $label->setScale($scale);
        $label->setTextSize($size);
        
        $label->setTextEmboss();
        $label->setAlign("left", "center");
        $this->frame->addComponent($label);

        $icon = new \ManiaLib\Gui\Elements\Quad(5, 5);
        $icon->setStyle("BgRaceScore2");
        $icon->setSubStyle("SendScore");  
        $icon->setAlign("right", "center");
        $this->icons->addComponent($icon);        

        $script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Maps\Gui\Scripts_CurrentMap");
        $this->registerScript($script);

        $this->setName("Current Map Widget");
    }

    function setAction($action) {
        $this->bg->setAction($action);
    }

    function destroy() {
        parent::destroy();
    }

}

?>
