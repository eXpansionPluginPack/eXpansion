<?php

namespace ManiaLivePlugins\eXpansion\Adm\Structures;

use Maniaplanet\DedicatedServer\Structures\AbstractStructure;

class CustomPoint extends AbstractStructure
{
    public $name;
    public $points;

    public function __construct($name, $points)
    {
        $this->name = $name;
        $this->points = $points;
    }
}
