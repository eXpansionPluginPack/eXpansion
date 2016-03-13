<?php

namespace ManiaLivePlugins\eXpansion\Votes;

class Config extends \ManiaLib\Utils\Singleton {

    public $restartLimit = 0;
    public $use_votes = true;
    public $global_timeout = 30;
    public $limit_votes = 1;
    public $restartVote_useQueue = true;  // Use track queue instead of instant restart, if 'eXpansion\Maps' plugin is loaded	
    public $managedVote_enable = array("NextMap" => true,
	"RestartMap" => true,
	"Kick" => true,
	"Ban" => true,
	"SetModeScriptSettingsAndCommands"=>true,
	"JumpToMapIdent"=>true,
	"SetNextMapIdent"=>true,
	"AutoTeamBalance"=>true);
    public $managedVote_commands = array("NextMap", "RestartMap", "Kick", "Ban", "SetModeScriptSettingsAndCommands", "JumpToMapIdent", "SetNextMapIdent", "AutoTeamBalance");
    public $managedVote_ratios = array("NextMap" => 0.5,
	"RestartMap" => 0.5,
	"Kick" => 0.6,
	"Ban" => -1.,
	"SetModeScriptSettingsAndCommands"=>-1.,
	"JumpToMapIdent"=>-1.,
	"SetNextMapIdent"=>-1.,
	"AutoTeamBalance"=>0.5);
    public $managedVote_timeouts = array("NextMap" => 30,
	"RestartMap" => 30,
	"Kick" => 30, 
	"Ban" => 30, 
	"SetModeScriptSettingsAndCommands"=>60, 
	"JumpToMapIdent"=>60, 
	"SetNextMapIdent"=>30, 
	"AutoTeamBalance"=>30);
    public $managedVote_voters = array("NextMap" => 1, 
	"RestartMap" => 1, 
	"Kick" => 1, 
	"Ban" => 1, 
	"SetModeScriptSettingsAndCommands"=>1, 
	"JumpToMapIdent"=>1, 
	"SetNextMapIdent"=>1, 
	"AutoTeamBalance"=>1);

}

?>
