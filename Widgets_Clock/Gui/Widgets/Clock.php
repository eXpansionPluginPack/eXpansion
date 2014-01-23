<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Clock\Gui\Widgets;

class Clock extends \ManiaLivePlugins\eXpansion\Gui\Windows\Widget {

    protected $clock;
    protected $clockBg;
    
    protected $date;
    protected $nameBg;

    protected function onConstruct() {
        parent::onConstruct();
            
        $this->clockBg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(22, 5);
        $this->addComponent($this->clockBg);
        $this->clockBg->setPosition(0, -8);
        
        $this->clock = new \ManiaLib\Gui\Elements\Label();
        $this->clock->setId('clock');
        $this->clock->setPosition(2, -6);
        $this->clock->setTextColor('fff');
        $this->clock->setScale(0.8);
        $this->clock->setStyle('TextCardScores2');
        //$this->clock->setTextPrefix('$s');
        $this->addComponent($this->clock);

        
        $this->nameBg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(57, 5);
        $this->addComponent($this->nameBg);
        $this->nameBg->setPosition(0, -3);
        
        $this->date = new \ManiaLib\Gui\Elements\Label(50, 6);
        $this->date->setId('date');
        $this->date->setAlign("left", "top");
        $this->date->setPosition(2, -1);
        $this->date->setTextColor('fff');
        $this->date->setTextPrefix('$s');
        $this->date->setAction(\ManiaLivePlugins\eXpansion\ServerStatistics\ServerStatistics::$serverStatAction);
        $this->addComponent($this->date);

        $script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Widgets_Clock\Gui\Scripts_Clock");
        $this->registerScript($script);
        $this->setName("Clock & Server Name Widget");
    }

    public function setServername($name) {
        $this->date->setText($name);
    }

    function destroy() {
        $this->clearComponents();
        parent::destroy();
    }

}

?>
