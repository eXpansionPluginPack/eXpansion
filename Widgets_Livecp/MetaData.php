<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Livecp;

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
        $this->setName(" Checkpoint Progress");
        $this->setDescription("Shows Checkpoint progress for players");
        $this->setGroups(array('Widgets'));
    }
}
