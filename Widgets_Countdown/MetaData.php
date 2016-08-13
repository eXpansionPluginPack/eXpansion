<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Countdown;

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
        $this->setName("Widget: Countdown");
        $this->setDescription("Provides Countdown timer for event");
        $this->setGroups(array('Widgets'));
    }

}
