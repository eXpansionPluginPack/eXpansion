<?php

namespace ManiaLivePlugins\eXpansion\Debugtool;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeString;

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
        $this->setName("Developers: DebugTool");
        $this->setDescription('Debugtool for developers');
        $this->setGroups(array('Tools'));

    }
}

?>
