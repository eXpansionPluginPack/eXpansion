<?php

namespace ManiaLivePlugins\eXpansion\Widgets_ServerInfo;

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
        $this->setName("Widget: Server infos");
        $this->setDescription("Provides server infos widget");
        $this->setGroups(array('Widgets'));
    }
}
