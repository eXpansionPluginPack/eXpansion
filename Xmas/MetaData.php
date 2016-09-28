<?php

namespace ManiaLivePlugins\eXpansion\Xmas;

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
        $this->setName("Widget: Chrismas lights");
        $this->setDescription("Seasonal widget just for fun");
        $this->setGroups(array('Widgets'));
    }
}
