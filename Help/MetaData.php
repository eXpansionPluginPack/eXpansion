<?php

namespace ManiaLivePlugins\eXpansion\Help;

/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData
{

    public function onBeginLoad()
    {

        $this->setName("Tools: Help");
        $this->setDescription("Provides help");
        $this->setGroups(array('Tools'));
    }
}
