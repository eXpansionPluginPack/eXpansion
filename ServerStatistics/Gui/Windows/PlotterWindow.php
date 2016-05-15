<?php

namespace ManiaLivePlugins\eXpansion\ServerStatistics\Gui\Windows;

/**
 * Description of PlotterWindow
 *
 * @author Reaby
 */
class PlotterWindow extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    protected $ok;
    protected $cancel;
    protected $plotter;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->plotter = new \ManiaLivePlugins\eXpansion\Gui\Elements\LinePlotter(160, 100);
        $this->plotter->setPosY(-5);
        $this->plotter->setPosX(5);
        $this->plotter->setLimits(0, 0, 100, 100);
        $this->addComponent($this->plotter);
        $this->plotter->setTickSize(5);
    }

    public function setDatas($datas)
    {
        foreach ($datas as $i => $data) {
            foreach ($data as $x => $val) {
                $val = $this->getNumber($val);
                $this->plotter->add($i, $x . ".0", $val);
            }
        }
    }

    public function setLineColor($line, $color)
    {
        $this->plotter->setLineColor($line, $color);
    }

    public function setLimit($x, $y)
    {
        $this->plotter->setLimits(0, 0, $x, $y);
    }

    public function setXLabels($labels)
    {
        $this->plotter->setXLabels($labels);
    }

    public function setYLabels($labels)
    {
        $this->plotter->setYLabels($labels);
    }

    private function getNumber($number)
    {
        return number_format((float)$number, 2, '.', '');
    }

    public function destroy()
    {
        $this->destroyComponents();
        parent::destroy();
    }

}
