<?php

namespace ManiaLivePlugins\eXpansion\MapRatings\Structures;

class MapRating extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure {

    public $rating;
    public $totalvotes;

    /** @var \Maniaplanet\DedicatedServer\Structures\Map */
    public $map;

    function __construct(\ManiaLivePlugins\eXpansion\MapRatings\Structures\Rating $rating, \Maniaplanet\DedicatedServer\Structures\Map $map) {
	$this->rating = round(($rating->rating / 5) * 100);
	$this->totalvotes = $rating->totalvotes;
	$this->map = $map;
    }

}

?>
