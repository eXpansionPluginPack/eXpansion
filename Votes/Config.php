<?php

namespace ManiaLivePlugins\eXpansion\Votes;

class Config extends \ManiaLib\Utils\Singleton {

    public $use_votes = true;
    public $global_timeout = 30;
    public $limit_votes = 1;
    public $restartVote_useQueue = true;  // Use track queue instead of instant restart, if 'eXpansion\Maps' plugin is loaded	
    public $managedVote_enable = array(true, true, true, true, true, true, true, true);
    public $managedVote_commands = array("NextMap", "RestartMap", "Kick", "Ban", "SetModeScriptSettingsAndCommands", "JumpToMapIdent", "SetNextMapIdent", "AutoTeamBalance");
    public $managedVote_ratios = array(0.5, 0.5, 0.6, -1., -1., -1., -1., 0.5);
    public $managedVote_timeouts = array(30, 30, 30, 30, 60, 60, 30, 30);
    public $managedVote_voters = array(1, 1, 1, 1, 1, 1, 1, 1);

}

?>
