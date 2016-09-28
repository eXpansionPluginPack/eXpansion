<?php

namespace ManiaLivePlugins\eXpansion\AutoUpdate;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
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
        $this->setName("Core: Auto Update");
        $this->setDescription("Provides auto update service requests and ingame updates");
        $this->setGroups(array('Core'));

        $config = Config::getInstance();
        $var = new Boolean("autoCheckUpdates", "Auto check updates when administator connects ?", $config);
        $var->setDescription("!! This won't work with git update !!");
        $var->setDefaultValue(true);
        $var->setGroup("Auto Update");
        $this->registerVariable($var);

        $config = Config::getInstance();
        $var = new Boolean("useGit", "Use git to update server", $config);
        $var->setDescription("!! You need to have git installed for this to work !!");
        $var->setDefaultValue(true);
        $var->setGroup("Auto Update");
        $this->registerVariable($var);

        $config = Config::getInstance();
        $var = new TypeString("branchName", "Name of the branch to update with", $config);
        $var->setDescription("master : Stable, nighty : latest fixes not tested, dev : just might work :D");
        $var->setDefaultValue('master');
        $var->setGroup("Auto Update");
        $this->registerVariable($var);
    }
}
