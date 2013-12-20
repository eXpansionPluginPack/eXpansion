<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;

class LinePlotter extends \ManiaLive\Gui\Control {

    protected $label_minX;
    protected $label_maxX;
    protected $label_minY;
    protected $label_maxY;
    protected $label_graphtitle;
    protected $graph_element;
    protected $graph;
    private $plots = array();
    private $limits = array();
    private $colors = array();

    /**
     * Button
     * 
     * @param int $sizeX = 24
     * @param intt $sizeY = 6
     */
    function __construct($sizeX = 100, $sizeY = 100) {
        $this->plots = array();

        $this->colors = array();
        $this->graph = new Graph($sizeX, $sizeY);
        $this->graph->setScriptEvents();
        $this->graph->setId("graph");
        $this->graph->setAlign("left", "top");
        $this->graph->setPosZ($this->getPosZ() + 10);
        $this->graph->setPosition(0, 0);
        $this->addComponent($this->graph);

        $this->limits = array(0, 0, 300, 100);
        $this->setLineColor(0);
        $this->setLineColor(1);
        $this->setLineColor(2);
    }

    public function add($line = 0, $x = 0, $y = 0) {
        if ($line > 2)
            throw new Exception("line number too big");
        $this->plots[$line][] = array($x, $y);
    }

    public function setLimits($minX, $minY, $maxX, $maxY) {
        $this->limits = array($minX, $minY, $maxX, $maxY);
    }

    public function setLineColor($line, $color = "000") {
        $r = (float) base_convert(substr($color, 0, 1), 16, 10) / 15;
        $g = (float) base_convert(substr($color, 1, 1), 16, 10) / 15;
        $b = (float) base_convert(substr($color, 2, 1), 16, 10) / 15;
        $r = $this->getNumber($r);
        $g = $this->getNumber($g);
        $b = $this->getNumber($b);
        $this->colors[$line] = array($r, $g, $b);
    }

    private function getNumber($number) {
        return number_format((float) $number, 2, '.', '');
    }

    public function getScript() {
        $val = '
declare CMlGraph Graph = (Page.GetFirstChild("graph") as CMlGraph);
log(Graph);

Graph.CoordsMin = <' . $this->getNumber($this->limits[0]) . ',' . $this->getNumber($this->limits[1]) . '>;
Graph.CoordsMax = <' . $this->getNumber($this->limits[2]) . ', ' . $this->getNumber($this->limits[3]) . '>;

declare CMlGraphCurve[] Curves = [Graph.AddCurve(), Graph.AddCurve(), Graph.AddCurve()];
declare CMlGraphCurve[] scaleX;
';
        for ($u = 0; $u < sizeof($this->plots); $u++) {
            for ($i = 0; $i < sizeof($this->plots[$u]); $i++) {
                $val .= "Curves[" . $u . "].Points.add(<" . $this->getNumber($this->plots[$u][$i][0]) . "," . $this->getNumber($this->plots[$u][$i][1]) . ">);\n";
            }
        }
        $val .= '
            

Curves[0].Color = <' . $this->colors[0][0] . ', ' . $this->colors[0][1] . ', ' . $this->colors[0][2] . '>;
Curves[1].Color = <' . $this->colors[1][0] . ', ' . $this->colors[1][1] . ', ' . $this->colors[1][2] . '>;
Curves[2].Color = <' . $this->colors[2][0] . ', ' . $this->colors[2][1] . ', ' . $this->colors[2][2] . '>;

';
        return $val;
    }

}

?>