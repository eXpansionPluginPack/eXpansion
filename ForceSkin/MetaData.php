<?php

namespace ManiaLivePlugins\eXpansion\ForceSkin;

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
        $this->setName("Tools: Force Skin");
        $this->setDescription("Forces a skin to be used for all player on a server");
        $this->setGroups(array('Tools'));

        $config = Config::getInstance();

        $var = new TypeString("skinUrl", "skin url address", $config, false, false);
        $var->setDefaultValue("");
        $this->registerVariable($var);

        $var = new TypeString("name", "set name for the skin", $config, false, false);
        $var->setDefaultValue("");
        $this->registerVariable($var);

        $this->setRelaySupport(false);
    }
}
