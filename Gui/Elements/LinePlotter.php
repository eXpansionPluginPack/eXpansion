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
    private $sizes = array();
    private $tickSize = 4;

    /**
     * Button
     * 
     * @param int $sizeX = 24
     * @param intt $sizeY = 6
     */
    function __construct($sizeX = 100, $sizeY = 100) {
        $this->plots = array();
        $this->sizes = Array($sizeX, $sizeY);

        $this->colors = array();
        $this->graph = new Graph($sizeX - 8, $sizeY - 4);
        $this->graph->setScriptEvents();
        $this->graph->setId("graph");
        $this->graph->setAlign("left", "top");
        $this->graph->setPosZ($this->getPosZ() + 10);
        $this->graph->setPosition(8, 0);
        $this->addComponent($this->graph);

        $this->limits = array(0, 0, 100, 100);
        $this->setLineColor(0);

        $this->label_maxY = new \ManiaLib\Gui\Elements\Label();
        $this->label_maxY->setPosition(0, 0);
        $this->label_maxY->setAlign("top", "right");
        $this->addComponent($this->label_maxY);

        $this->label_minY = new \ManiaLib\Gui\Elements\Label();
        $this->label_minY->setPosition(-2, -$sizeY);
        $this->label_minY->setAlign("top", "left");
        $this->addComponent($this->label_minY);

        $this->label_maxX = new \ManiaLib\Gui\Elements\Label(8);
        $this->label_maxX->setPosition($sizeX - 8, -$sizeY + 4);
        $this->label_maxX->setAlign("top", "right");
        $this->addComponent($this->label_maxX);

        $this->label_minX = new \ManiaLib\Gui\Elements\Label();
        $this->label_minX->setPosition(4, -$sizeY + 4);
        $this->label_minX->setAlign("top", "right");
        $this->addComponent($this->label_minX);
    }

    public function add($line = 0, $x = 0, $y = 0) {
        $this->plots[$line][] = array($x, $y);
    }

    public function setLimits($minX, $minY, $maxX, $maxY) {
        $this->limits = array($minX, $minY, $maxX, $maxY);
        $this->label_maxX->setText($maxX);
        $this->label_minX->setText($minX);
        $this->label_maxY->setText($maxY);
        $this->label_minY->setText($minY);
    }

    /**
     * sets the step value for scale-lines
     * @param float $step
     */
    public function setTickSize($step = 4) {
        $this->tickSize = $step;
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
        $y = $this->getNumber($this->sizes[1]);
        $x = $this->getNumber($this->sizes[0]);

        $val = '
declare CMlGraph Graph = (Page.GetFirstChild("graph") as CMlGraph);
log(Graph);

Graph.CoordsMin = <' . $this->getNumber($this->limits[0]) . ',' . $this->getNumber($this->limits[1]) . '>;
Graph.CoordsMax = <' . $this->getNumber($this->limits[2]) . ', ' . $this->getNumber($this->limits[3]) . '>;

declare CMlGraphCurve[] Curves;
declare CMlGraphCurve[] scaleX;
';
        $index = 0;
        foreach ($this->plots as $u => $plot) {
            $val .= 'Curves.add(Graph.AddCurve());' . "\n";
            foreach ($plot as $i => $vals) {
                $val .= "Curves[" . $index . "].Points.add(<" . $this->getNumber($this->plots[$u][$i][0]) . "," . $this->getNumber($this->plots[$u][$i][1]) . ">);\n";
            }
            $index++;
        }


        foreach ($this->colors as $u => $color) {
            $val .= 'Curves[' . $u . '].Color = <' . $color[0] . ', ' . $color[1] . ', ' . $color[2] . '>;';
        }

        $val .= '       
declare min = (Graph.CoordsMin[1]);
declare max = (Graph.CoordsMax[1]);
declare diff = Graph.CoordsMax[1] - Graph.CoordsMin[1];
declare Real base = MathLib::Ln(diff)/2.303; 
declare Real power =MathLib::ToReal(MathLib::NearestInteger(base));
declare Real base_unit = MathLib::Pow(10.0,power);
declare Real step = base_unit / ' . $this->getNumber($this->tickSize) . ';
declare Integer i;
 
//  initial scaleX (| lines)
    scaleX.add(Graph.AddCurve());
   
    scaleX[0].Points.add(<Graph.CoordsMin[0], Graph.CoordsMin[1]>);
    scaleX[0].Points.add(<Graph.CoordsMin[0]+0.001, Graph.CoordsMax[1]>);
    scaleX[0].Color = <0.0, 0.0, 0.0>;
    scaleX[0].Width = 0.5; 

// loop for creating scaleY (- lines)
declare Real index = min;
while (index < max) {
    scaleX.add(Graph.AddCurve());
    i = scaleX.count-1;
    scaleX[i].Points.add(<Graph.CoordsMin[0], index>);
    scaleX[i].Points.add(<Graph.CoordsMax[0], index>);
    scaleX[i].Color = <0.0, 0.0, 0.0>;
 //   scaleX[i].Width = 0.5; 
    index = index + step;
}
';
        return $val;
    }
}

?>