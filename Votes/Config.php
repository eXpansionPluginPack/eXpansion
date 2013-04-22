<?php

namespace ManiaLivePlugins\eXpansion\Votes;

class Config extends \ManiaLib\Utils\Singleton {

	public $restartVote_enable = true;
	public $restartVote_ratio = -1;       // -1 = server default, otherwise 0.00 - 1.00
	public $restartVote_timeout = 0;     // '0' for server default, '1' for indefinite, otherwise set number of desired seconds
	public $restartVote_voters = 1;       // '0' means only active players, '1' means any player, '2' is for everybody, pure spectators included
	public $restartVote_useQueue = true;  // Use track queue instead of instant restart, if 'eXpansion\Maps' plugin is loaded
	
	public $skipVote_enable = true;
	public $skipVote_ratio = -1;     // -1 = server default, otherwise 0.00 - 1.00
	public $skipVote_timeout = 0;   // '0' for server default, '1' for indefinite, otherwise set number of desired seconds
	public $skipVote_voters = 1;    // '0' means only active players, '1' means any player, '2' is for everybody, pure spectators included
	public $skipVote_limit = -1;      // Limits skip votes to within set number of seconds from beginning of match; '-1' or false to disable, set to desired # of seconds


}
?>
