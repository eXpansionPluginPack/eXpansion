<?php

namespace ManiaLivePlugins\eXpansion\Faq;

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
        $this->setName("eXpansion Help");
        $this->setDescription("Provides ingame help with frequently asked question");
        $this->setGroups(array("Tools"));
    }

}
