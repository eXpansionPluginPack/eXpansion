<?php

namespace ManiaLivePlugins\eXpansion\Widgets_LocalScores;

/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData
{

    public function onBeginLoad()
    {

        $this->setName("Widget: Local Scores");
        $this->setDescription("Local scores widget, can be used when local records are in points instead of time");
        $this->setGroups(array('Widgets', 'Records'));
    }
}
