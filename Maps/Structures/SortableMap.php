<?php

namespace ManiaLivePlugins\eXpansion\Maps\Structures;

/**
 * Structure mapWish
 *
 * @author Reaby
 */
class SortableMap
{

    /** @var \Maniaplanet\DedicatedServer\Structures\Map */
    public $map;

    /** @var string */
    public $author, $name, $style;

    /** @var int */
    public $goldtime, $localrecord, $localmax;

    /** @var \ManiaLivePlugins\eXpansion\MapRatings\Structures\Rating */
    public $rating;

    public $maxrec;

    /**
     *
     * @param \Maniaplanet\DedicatedServer\Structures\Map $map
     * @param int $localrec
     * @param \ManiaLivePlugins\eXpansion\MapRatings\Structures\Rating $rating
     */
    public function __construct(\Maniaplanet\DedicatedServer\Structures\Map $map, $localrec, $maxrec, \ManiaLivePlugins\eXpansion\MapRatings\Structures\Rating $rating)
    {
        $this->map = $map;
        $this->name = \ManiaLib\Utils\Formatting::stripStyles($map->name);
        $this->author = $map->author;
        $this->goldTime = $map->goldTime;
        $this->style = $map->mapStyle; // for future use
        $this->localrecord = $localrec;
        $this->rating = $rating;
        $this->maxrec = $maxrec;
    }

}

?>
