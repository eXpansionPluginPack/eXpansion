<?php

namespace ManiaLivePlugins\eXpansion\Widgets_PlainLocalRecords;

/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData
{

    public function onBeginLoad()
    {

        $this->setName(" Plain Local Records");
        $this->setDescription("LocalRecords without maniascript");
        $this->setGroups(array('Widgets', 'Records'));
    }
}
