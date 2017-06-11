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
        $this->setName("Map Suggestions");
        $this->setDescription("..Be sure to load also the widget");
        $this->setGroups(array('Maps'));
    }
}
