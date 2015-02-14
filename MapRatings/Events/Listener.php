<?php

namespace ManiaLivePlugins\eXpansion\MapRatings\Events;

use ManiaLive\Event\Listener as EventListener;
use ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record;

/**
 * Listener interface for local records
 * @author Petri
 */
interface Listener extends EventListener
{

	/**
	 * Event triggered on new rating
	 * 
	 * @param \ManiaLivePlugins\eXpansion\MapRatings\Structures\PlayerVote[] $ratings
	 */
	function OnRatingsSave($ratings);
	
}

?>
