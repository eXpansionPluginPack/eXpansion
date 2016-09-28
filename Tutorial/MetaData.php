<?php

namespace ManiaLivePlugins\eXpansion\Tutorial;

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
        $this->setName("Tutorial");
        $this->setDescription("Provides onetime popup tutorial for players on how to use eXpansion");
    }
}
