<?php
namespace ManiaLivePlugins\eXpansion\MapRatings\Structures;

class Rating extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure {

    public $rating;
    public $totalvotes;
    
    function __construct($rating, $total) {
        $this->rating = $rating;
        $this->totalvotes = $total;
    }

}
?>
