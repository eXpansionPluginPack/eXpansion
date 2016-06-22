<?php

namespace ManiaLivePlugins\eXpansion\ScoreDisplay;

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
        $this->setName("Games: ScoreDisplay");
        $this->setDescription("Show scores for a match");
        $this->setGroups(array('Games'));
    }

}
