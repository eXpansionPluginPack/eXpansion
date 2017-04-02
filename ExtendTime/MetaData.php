<?php

namespace ManiaLivePlugins\eXpansion\ExtendTime;

use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeInt;

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
        $this->setName("Tools: Extend Time");
        $this->setDescription("Provides Votes to Extend timelimit on  a map");
        $this->setGroups(array('Tools'));

        $config = Config::getInstance();
        $var = new TypeInt("timelimit", "Default timelimit to set", $config, false, false);
        $var->setDefaultValue(300);
        $this->registerVariable($var);
    }
}
