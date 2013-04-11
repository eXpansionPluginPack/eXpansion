<?php

namespace ManiaLivePlugins\eXpansion\MapRatings;

class Config extends \ManiaLib\Utils\Singleton {

    public $sendBeginMapNotices = true;     // Sends chat message of  current rating at map start
    public $msg_rating = '#rating#Map Approval Rating: #variable#%2$s#rating# (#variable#%3$s #rating#votes).  Your Rating: #variable#%4$s#rating# / #variable#5';  // '%1$s' = Map Name, '%2$s' = Rating %, '%3$s' = # of Ratings, '%4$s' = Player's Rating
    public $msg_noRating = '#rating# $iMap has not been rated yet!';

}
?>
