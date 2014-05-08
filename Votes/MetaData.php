<?php

namespace ManiaLivePlugins\eXpansion\Votes;

use ManiaLivePlugins\eXpansion\Core\types\config\types\String;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\HashList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Int;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedInt;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Float;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedFloat;

/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData {

    public function onBeginLoad() {
	parent::onBeginLoad();
	$this->setName("Votes");
	$this->setDescription("Provides Custom Votes handler for eXpansion");
	$config = Config::getInstance();

	$var = new Boolean("use_votes", "Enable managed voting for this server ?", $config);
	$var->setGroup("Voting");
	$var->setDefaultValue(true);
	$this->registerVariable($var);

	$var = new Int("limit_votes", "Limit voting for a player on map", $config);
	$var->setDescription("-1 to disable, othervice number of vote start");
	$var->setGroup("Voting");
	$var->setDefaultValue(1);
	$this->registerVariable($var);

	$var = new Boolean("restartVote_useQueue", "Use track queue instead of intant restart for replay votes ?", $config);
	$var->setGroup("Voting");
	$var->setDefaultValue(true);
	$this->registerVariable($var);

	$var = new HashList("managedVote_enable", "Use eXp managed votes ?", $config);
	$type = new Boolean("", "", null);
	$var->setType($type);
	$var->setVisible(false);
	$var->setDefaultValue(array("NextMap" => true,
	    "RestartMap" => true,
	    "Kick" => true,
	    "Ban" => true,
	    "SetModeScriptSettingsAndCommands" => true,
	    "JumpToMapIdent" => true,
	    "SetNextMapIdent" => true,
	    "AutoTeamBalance" => true));
	$this->registerVariable($var);

	$var = new BasicList("managedVote_commands", "Managed vote commands", $config);
	$type = new String("", "", null);
	$var->setType($type);
	$var->setVisible(false);
	$var->setGroup("Voting");
	$var->setDefaultValue(array("NextMap", "RestartMap", "Kick", "Ban", "SetModeScriptSettingsAndCommands", "JumpToMapIndex", "SetNextMapIndex", "AutoTeamBalance"));
	$this->registerVariable($var);

	$var = new HashList("managedVote_ratios", "Managed vote ratios", $config);
	$var->setDescription("set ratio -1 for disable, and ratio between 0 to 1");
	$type = new BoundedFloat("", "", null);
	$type->setMin(-1.0);
	$type->setMax(1.0);
	$var->setVisible(false);
	$var->setType($type);
	$var->setGroup("Voting");
	$var->setDefaultValue(array("NextMap" => 0.5,
	    "RestartMap" => 0.5,
	    "Kick" => 0.6,
	    "Ban" => -1.,
	    "SetModeScriptSettingsAndCommands" => -1.,
	    "JumpToMapIdent" => -1.,
	    "SetNextMapIdent" => -1.,
	    "AutoTeamBalance" => 0.5));
	$this->registerVariable($var);

	$var = new HashList("managedVote_timeouts", "Managed vote timeouts", $config);
	$var->setDescription("time in seconds");
	$type = new Int("", "", null);
	$var->setType($type);
	$var->setVisible(false);
	$var->setGroup("Voting");
	$var->setDefaultValue(array("NextMap" => 30,
	    "RestartMap" => 30,
	    "Kick" => 30,
	    "Ban" => 30,
	    "SetModeScriptSettingsAndCommands" => 60,
	    "JumpToMapIdent" => 60,
	    "AutoTeamBalance" => 30,
	    "AutoTeamBalance" => 30));
	$this->registerVariable($var);

	$var = new HashList("managedVote_voters", "Managed vote voters", $config);
	$type = new BoundedInt("", "", null);
	$type->setMin(0);
	$type->setMax(2);
	$var->setVisible(false);
	$var->setType($type);
	$var->setGroup("Voting");
	$var->setDefaultValue(array("NextMap" => 1,
	    "RestartMap" => 1,
	    "Kick" => 1,
	    "Ban" => 1,
	    "SetModeScriptSettingsAndCommands" => 1,
	    "JumpToMapIdent" => 1,
	    "SetNextMapIdent" => 1,
	    "AutoTeamBalance" => 1));
	$this->registerVariable($var);
    }

}
