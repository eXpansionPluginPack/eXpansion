<?php

namespace ManiaLivePlugins\eXpansion\Debugtool;

/**
 * Description of MetaData
 *
 * @author De Cramer Oliver
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData
{

    public function onBeginLoad()
    {
        parent::onBeginLoad();
        $this->setName(" Debug Tool");
        $this->setDescription('Debugtool for developers');
        $this->setGroups(array('Tools'));
    }
}
