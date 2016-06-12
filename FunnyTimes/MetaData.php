<?php

namespace ManiaLivePlugins\eXpansion\FunnyTimes;

use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeString;

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
        $this->setName("Other: Funny Times");
        $this->setDescription("displays message on funny time");
        $this->setGroups(array('Other'));
    }

}
