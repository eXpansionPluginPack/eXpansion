<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

class Marker extends \ManiaLib\Gui\Element
{

    protected $xmlTagName = 'marker';
    protected $posX = 0;
    protected $posY = 0;
    protected $posZ = 0;

    public function __construct($sizeX = 20, $sizeY = 6)
    {
        parent::__construct($sizeX, $sizeY);
    }

    protected function postFilter()
    {
    }
}
