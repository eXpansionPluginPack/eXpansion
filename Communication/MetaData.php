<?php

namespace ManiaLivePlugins\eXpansion\Communication;

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
        $this->setName(' Multi-tab personal messages');
        $this->setDescription('Provides nextgen commmunication platform for serverside personal messaging');
        $this->setGroups(array('Chat', 'Widgets'));
    }
}
