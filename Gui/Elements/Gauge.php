<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

class Gauge extends \ManiaLib\Gui\Element {

    const EnergyBar = "EnergyBar";
    const BgCard = "BgCard";
    const ProgressBar = "ProgressBar";
    const ProgressBarSmall = "ProgressBarSmall";
    
    
    protected $xmlTagName = 'gauge';
    protected $posX = 0;
    protected $posY = 0;
    protected $posZ = 0;
    protected $colorize = null;
    protected $grading = 1.0;
    protected $ratio = 0.0;
    protected $style = self::EnergyBar;
    protected $drawBg = false;    
    protected $drawBlockBg = true;    
    
    function __construct($sizeX = 20, $sizeY = 6) {
	$this->sizeX = $sizeX;
	$this->sizeY = $sizeY;
    }

    /**
     * @param string $color
     */
    function setColorize($color) {
	$this->colorize = $color;
    }

    function getColorize() {
	return $this->colorize;
    }
    
    /**
     * @param string $color
     */
    function setGrading($grading) {
	$this->grading = $grading;
    }

    function getGrading() {
	return $this->grading;
    }
    
        /**
     * @param string $color
     */
    function setRatio($ratio) {
	$this->ratio = $ratio;
    }

    function getRatio() {
	return $this->ratio;
    }
    
    protected function postFilter() {
	if ($this->colorize !== null)
	    $this->xml->setAttribute('color', $this->colorize);
	if ($this->grading !== null)
	    $this->xml->setAttribute('grading', $this->grading);
	if ($this->ratio !== null)
	    $this->xml->setAttribute('ratio', $this->ratio);
	$this->xml->setAttribute('drawbg', $this->drawBg ? 1 : 0 );
	$this->xml->setAttribute('drawblockbg', $this->drawBlockBg ? 1 : 0 );
    }

}
