<?php

namespace ManiaLivePlugins\eXpansion\MapRatings\Structures;

class MapRating extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure {

    public $rating;
    public $totalvotes;

    /** @var \Maniaplanet\DedicatedServer\Structures\Map */
    public $map;

    function __construct(\ManiaLivePlugins\eXpansion\MapRatings\Structures\Rating $rating = null, \Maniaplanet\DedicatedServer\Structures\Map $map = null) {
	if($rating != null){
	    $this->rating = round(($rating->rating / 5) * 100);
	    $this->totalvotes = $rating->totalvotes;
	    $this->map = $map;
	}
    }
    

}

?>
