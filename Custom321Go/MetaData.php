<?php

namespace ManiaLivePlugins\eXpansion\Custom321Go;

use ManiaLivePlugins\eXpansion\Core\types\config\types\HashList;
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
        $this->setName("Tool: 3-2-1-GO Customizer");
        $this->setDescription("Replaces the 3-2-1-Go with custom images");
        $this->setGroups(array(' Tools'));
        $config = Config::getInstance();

        $var = new TypeString("sprite1", "3-2-1", $config, false, false);
        $var->setDescription("image for 3-2-1");
        $var->setDefaultValue("http://reaby.kapsi.fi/ml/ghost.png");
        $this->registerVariable($var);

        $var = new TypeString("sprite2", "GO!", $config, false, false);
        $var->setDescription("image for go");
        $var->setDefaultValue("http://reaby.kapsi.fi/ml/boo.png");
        $this->registerVariable($var);

        //$this->setRelaySupport(false);
    }

}
