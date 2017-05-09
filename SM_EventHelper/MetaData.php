<?php

namespace ManiaLivePlugins\eXpansion\SM_EventHelper;

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

        $this->setName("Helper used for PlatformBeta@nadeolabs events");
        $this->setDescription("Event helper, needed for the platform scores to work");
        $this->setGroups(array('Helpers'));

        $this->addTitleSupport("SM");
    }
}
