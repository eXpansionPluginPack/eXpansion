<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;
use ManiaLivePlugins\eXpansion\Adm\Gui\Controls\MatchSettingsFile;
use ManiaLive\Gui\ActionHandler;

class TestGraph extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    protected $ok;
    protected $cancel;
    private $actionOk;
    private $actionCancel;
    protected $plotter;

    protected function onConstruct() {
        parent::onConstruct();
        $this->plotter = new \ManiaLivePlugins\eXpansion\Gui\Elements\LinePlotter(160, 100);
        $this->plotter->setLimits(0, 0, 100, 100);
        $this->addComponent($this->plotter);
        for ($x = 0; $x < 100; $x+=5) {
            echo $x;
            $this->plotter->add(0, $x . ".0", rand(0, 100) . ".0");
            $this->plotter->add(1, $x . ".0", rand(0, 100) . ".0");
        }
        $this->plotter->setTickSize(5);
        $this->plotter->setLineColor(0, "f90");
        $this->plotter->setLineColor(1, "00f");
        $this->setTitle("testGraph");
    }
    
    function destroy() {
        $this->clearComponents();
        parent::destroy();
    }

}

?>
