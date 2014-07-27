<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Advertising;

class Config extends \ManiaLib\Utils\Singleton {
    
    public $url = "http://www.ml-expansion.com";
    public $imageUrl = "http://reaby.kapsi.fi/ml/exp_small.png";
    public $imageFocusUrl = "http://reaby.kapsi.fi/ml/exp_small.png";

    public $imageSizeX = 512; // image sizeX in px
    public $imageSizeY = 128; // image sizeY in px
    
    public $size = 30;  // image width in maniaplanet display units
    
    public $x = -30;  // image position x in maniaplanet display units
    public $y = 90; // image position y in maniaplanet display units
}

?>
