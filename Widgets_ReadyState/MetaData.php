<?php

namespace ManiaLivePlugins\eXpansion\Widgets_ReadyState;

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
        $this->setName("Widget: ReadyState");
        $this->setDescription("Shows players still loading the map!");
        $this->setGroups(array('Widgets'));
    }
}
