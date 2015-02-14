<?php

namespace ManiaLivePlugins\eXpansion\MapRatings\Events;

class Event extends \ManiaLive\Event\Event
{

	const ON_RATINGS_SAVE = 1;

	protected $params;

	function __construct($onWhat)
	{
		parent::__construct($onWhat);
		$params = func_get_args();
		array_shift($params);
		$this->params = $params;
	}

	function fireDo($listener)
	{
		$p = $this->params;
		switch ($this->onWhat) {
			case self::ON_RATINGS_SAVE: $listener->onRatingsSave($p[0]);
				break;
		}
	}

}

?>