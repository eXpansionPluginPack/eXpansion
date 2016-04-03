<?php

namespace ManiaLivePlugins\eXpansion\MapSuggestion;

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
        $this->setName("Maps: suggestions");
        $this->setDescription("Provides map suggestion core");
        $this->setGroups(array('Maps'));

    }

}
