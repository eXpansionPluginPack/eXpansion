<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Netlost;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;

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
        $this->setName(" Netlost status widget");
        $this->setDescription("Provides netlost infos");
        $this->setGroups(array('Widgets', 'Tools'));

        $configInstance = Config::getInstance();

        $var = new Boolean("showOnlyAdmins", "show widget only to admins", $configInstance, false, false);
        $var->setDefaultValue(true);
        $this->registerVariable($var);
    }
}
