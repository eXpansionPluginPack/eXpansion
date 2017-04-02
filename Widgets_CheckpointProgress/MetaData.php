<?php

namespace ManiaLivePlugins\eXpansion\Widgets_CheckpointProgress;

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
        $this->setName(" Checkpoints Progressbar");
        $this->setDescription("Provides Checkpoint progress widget");
        $this->setGroups(array('Widgets'));
    }
}
