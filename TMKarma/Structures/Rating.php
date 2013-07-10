<?php

namespace ManiaLivePlugins\eXpansion\TMKarma\Structures;

class Rating
{
	public $count;
	public $percent;
	
	function __construct($response)
	{
		$this->count = (int)$response['count'];
		$this->percent = $response['percent'];	
	}
}

?>