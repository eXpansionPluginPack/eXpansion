<?php

namespace ManiaLivePlugins\eXpansion\ServerStatistics\Gui\Windows;

/**
 * Description of PlotterWindow
 *
 * @author Reaby
 */
class PlotterWindow extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    protected $ok;
    protected $cancel;
    protected $plotter;

    protected function onConstruct() {
        parent::onConstruct();
        $this->plotter = new \ManiaLivePlugins\eXpansion\Gui\Elements\LinePlotter(160, 100);
        $this->plotter->setLimits(0, 0, 100, 100);
        $this->addComponent($this->plotter);
        $this->plotter->setTickSize(5);
    }

    function setDatas($datas) {
        foreach ($datas as $i => $data) {
            foreach ($data as $x => $val) {
                $val = $this->getNumber($val);
                $this->plotter->add($i, $x . ".0", $val);
            }
        }
    }

    function setLineColor($line, $color) {
        $this->plotter->setLineColor($line, $color);
    }

    function setLimit($x, $y) {
        $this->plotter->setLimits(0, 0, $x, $y);
    }

    private function getNumber($number) {
        return number_format((float) $number, 2, '.', '');
    }

    function destroy() {
        $this->clearComponents();
        parent::destroy();
    }

}
