<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

class Marker extends \ManiaLib\Gui\Element
{

    protected $xmlTagName = 'marker';
    protected $posX = 0;
    protected $posY = 0;
    protected $posZ = 0;

    function __construct($sizeX = 20, $sizeY = 6)
    {
        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
    }

    protected function postFilter()
    {

        /* if ($this->colorize !== null)
            $this->xml->setAttribute('color', $this->colorize);
        if ($this->grading !== null)
            $this->xml->setAttribute('grading', $this->grading);
        if ($this->ratio !== null)
            $this->xml->setAttribute('ratio', $this->ratio);
        $this->xml->setAttribute('drawbg', $this->drawBg ? 1 : 0 );
        $this->xml->setAttribute('drawblockbg', $this->drawBlockBg ? 1 : 0 );
         */
    }

}
