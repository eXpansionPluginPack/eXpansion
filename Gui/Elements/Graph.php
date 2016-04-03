<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

class Graph extends \ManiaLib\Gui\Element
{

    protected $xmlTagName = 'graph';
    protected $posX = 0;
    protected $posY = 0;
    protected $posZ = 0;

    function __construct($sizeX = 100, $sizeY = 100)
    {
        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
    }

}
