<?php

namespace ManiaLivePlugins\eXpansion\SubTest;

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
        $this->setName("Submenu Test plugin");
        $this->setDescription("Test plugin");
        $this->setGroups(array('Menu'));
    }

}
