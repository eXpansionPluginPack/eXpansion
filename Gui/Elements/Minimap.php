<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

class Minimap extends \ManiaLib\Gui\Element
{

    protected $xmlTagName = 'minimap';
    protected $mapPosX = 0;
    protected $mapPosY = 0;
    protected $mapPosZ = 0;
    protected $zoom = 1.0;

    public function __construct($sizeX = 20, $sizeY = 6)
    {
        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
    }

    protected function postFilter()
    {

    }
}
