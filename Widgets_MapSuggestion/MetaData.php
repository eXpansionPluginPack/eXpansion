<?php

namespace ManiaLivePlugins\eXpansion\Widgets_MapSuggestion;

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

        $this->setName("Widget: Suggest a map button");
        $this->setDescription("Map suggestion button");
        $this->setGroups(array('Widgets', 'Maps'));
    }

}

?>
