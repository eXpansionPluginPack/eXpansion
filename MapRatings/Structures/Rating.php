<?php
namespace ManiaLivePlugins\eXpansion\MapRatings\Structures;

class Rating extends \DedicatedApi\Structures\AbstractStructure {

    public $rating;
    public $totalvotes;
    
    function __construct($rating, $total) {
        $this->rating = $rating;
        $this->totalvotes = $total;
    }

}
?>
