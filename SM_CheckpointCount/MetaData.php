<?php

namespace ManiaLivePlugins\eXpansion\SM_CheckpointCount;

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

        $this->setName("Checkpoint Counter");
        $this->setDescription("Checkpoint counter for storm");
        $this->setGroups(array('Widgets', 'Records'));
        $this->addTitleSupport("SM");
    }
}
