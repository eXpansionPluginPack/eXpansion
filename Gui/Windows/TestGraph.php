<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

class TestGraph extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{
    protected $ok;
    protected $cancel;
    protected $plotter;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->plotter = new \ManiaLivePlugins\eXpansion\Gui\Elements\LinePlotter(160, 100);
        $this->plotter->setLimits(0, 0, 100, 100);
        $this->addComponent($this->plotter);
        for ($x = 0; $x <= 100; $x += 5) {
            $this->plotter->add(0, $x . ".0", rand(0, 100) . ".0");
            $this->plotter->add(1, $x . ".0", rand(0, 100) . ".0");
        }
        $this->plotter->setTickSize(5);
        $this->plotter->setLineColor(0, "f90");
        $this->plotter->setLineColor(1, "00f");
        $this->setTitle("testGraph");
    }

    public function destroy()
    {
        $this->destroyComponents();
        parent::destroy();
    }
}
