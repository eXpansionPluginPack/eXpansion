<?php

namespace ManiaLivePlugins\eXpansion\Votes;

use ManiaLivePlugins\eXpansion\Core\types\config\types\String;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList;
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

	$var = new Boolean("restartVote_enable", "Enable restart vote ?", $config);
	$var->setGroup("Voting");
	$var->setDefaultValue(true);
	$this->registerVariable($var);

	$var = new Boolean("skipVote_enable", "Enable skip vote ?", $config);
	$var->setGroup("Voting");
	$var->setDefaultValue(true);
	$this->registerVariable($var);

	$var = new Boolean("restartVote_useQueue", "Use track queue instead of intant restart ?", $config);
	$var->setGroup("Voting");
	$var->setDefaultValue(true);
	$this->registerVariable($var);

	$var = new BoundedFloat("restartVote_ratio", "Restart votes pass ratio ", $config);
	$var->setDescription("(-1 = server default, otherwise 0.00 - 1.00)");
	$var->setGroup("Voting");
	$var->setMin(-1.0);
	$var->setMax(1.0);
	$var->setDefaultValue(-1.0);
	$this->registerVariable($var);

	$var = new Int("restartVote_timeout", "Restart Vote Timeout", $config);
	$var->setGroup("Voting");
	$var->setDescription("('0' for server default, '1' for indefinite, otherwise set number of desired seconds)");
	$var->setDefaultValue(0);
	$this->registerVariable($var);

	$var = new BoundedInt("restartVote_voters", "Restart Voters", $config);
	$var->setGroup("Voting");
	$var->setDescription("('0' means only active players, '1' means any player, '2' is for everybody, pure spectators included)");
	$var->setMin(0);
	$var->setMax(2);
	$var->setDefaultValue(1);
	$this->registerVariable($var);

	$var = new BoundedFloat("skipVote_ratio", "Skip votes pass ratio ", $config);
	$var->setGroup("Voting");
	$var->setDescription("(-1 = server default, otherwise 0.00 - 1.00)");
	$var->setMin(-1.0);
	$var->setMax(1.0);
	$var->setDefaultValue(-1.0);
	$this->registerVariable($var);

	$var = new Int("skipVote_timeout", "Skip vote Timeout", $config);
	$var->setGroup("Voting");
	$var->setDescription("('0' for server default, '1' for indefinite, otherwise set number of desired seconds)");
	$var->setDefaultValue(0);
	$this->registerVariable($var);

	$var = new BoundedInt("skipVote_voters", "Skip Voters", $config);
	$var->setGroup("Voting");
	$var->setDescription("('0' means only active players, '1' means any player, '2' is for everybody, pure spectators included)");
	$var->setMin(0);
	$var->setMax(2);
	$var->setDefaultValue(1);
	$this->registerVariable($var);
    }

}
