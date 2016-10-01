<?php

namespace ManiaLivePlugins\eXpansion\MapRatings\Structures;

use Maniaplanet\DedicatedServer\Structures\Map;

class MapRating extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure
{

    public $rating;
    public $totalvotes;

    /** @var Map */
    public $map;

    public function __construct(Rating $rating = null, Map $map = null)
    {
        if ($rating != null) {
            $this->rating = round(($rating->rating / 5) * 100);
            $this->totalvotes = $rating->totalvotes;
            $this->map = $map;
        }
    }
}
