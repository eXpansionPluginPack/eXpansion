<?php
namespace ManiaLivePlugins\eXpansion\MapRatings\Structures;

class Rating extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure
{

    public $rating;
    public $totalvotes;
    public $uid;

    function __construct($rating, $total, $uid = "")
    {
        $this->rating = $rating;
        $this->totalvotes = $total;
        $this->uid = $uid;
    }

}
