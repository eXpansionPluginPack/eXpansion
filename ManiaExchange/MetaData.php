<?php

namespace ManiaLivePlugins\eXpansion\ManiaExchange;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedTypeFloat;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedTypeInt;
use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeString;

/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData
{

    public function onBeginLoad()
    {
        parent::onBeginLoad();
        $this->setName("Mania-Exchange integration");
        $this->setDescription("Provides integration for Mania-Exchange.com");
        $this->setGroups(array('Maps', 'Connectivity'));

        $config = Config::getInstance();

        $var = new Boolean("juke_newmaps", "Autojuke newly added maps ?", $config, true, false);
        $var->setDefaultValue(true);
        $this->registerVariable($var);

        $var = new Boolean(
            "mxVote_enable",
            "Allow players to temporarily add maps using votes ?",
            $config,
            false,
            false
        );
        $var->setGroup("Voting");
        $var->setDefaultValue(false);
        $this->registerVariable($var);

        $var = new BoundedTypeFloat("mxVote_ratios", "MXVote ratio for adding map", $config, true, false);
        $var->setGroup("Voting");
        $var->setMin(-1.0);
        $var->setMax(1.0);
        $var->setDefaultValue(0.5);
        $this->registerVariable($var);

        $var = new BoundedTypeInt("mxVote_timeouts", "MXVote Timeout in seconds", $config, true, false);
        $var->setDescription("Min: 30, Max: 360");
        $var->setGroup("Voting");
        $var->setMin(30);
        $var->setMax(360);
        $var->setDefaultValue(false);
        $this->registerVariable($var);


        $var = new BoundedTypeInt("mxVote_voters", "MXVote Limit", $config, true, false);
        $var->setGroup("Voting");
        $var->setMin(0);
        $var->setMax(2);
        $var->setDefaultValue(1);
        $this->registerVariable($var);

        $var = new TypeString('key', 'Mania-exchange key', $config, true, false);
        $var->setDescription("You know if you need this, otherwise leave empty");
        $var->setDefaultValue("");
        $this->registerVariable($var);

        $var = new TypeString('file_name', 'File Pattern', $config, true, false);
        $var->setDefaultValue('{server_title}/{map_author}_{map_name}_{mx_id}.map.gbx');
        $var->setDescription(
            array(
                'Pattern to define where the map will be saved. Available variables : ',
                '{map_author}, {map_name}, {map_environment}, {map_vehicle}, {map_type}',
                '{map_style}, {mx_id}, {server_title}',
                'You map use / to create sub directories.'
            )
        );
        $this->registerVariable($var);
    }
}
