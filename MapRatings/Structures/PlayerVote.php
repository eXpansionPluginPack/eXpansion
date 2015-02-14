<?php

namespace ManiaLivePlugins\eXpansion\MapRatings\Structures;

class PlayerVote extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure
{

	public $login;

	public $rating;

	function __construct($login = null, $vote = null)
	{
		$this->login = $login;
		$this->rating = $vote;
	}

}

?>
