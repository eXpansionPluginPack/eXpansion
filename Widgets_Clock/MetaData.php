<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Clock;

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
        $this->setName("Widget: Clock");
        $this->setDescription("Provides Local Time display");
        $this->setGroups(array('Widgets'));
    }

}
