<?php

namespace ManiaLivePlugins\eXpansion\Custom321Go;

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
        $this->setName(" 3-2-1-GO Customizer");
        $this->setDescription("Replaces the 3-2-1-Go with custom images");
        $this->setGroups(array('Tools', "Widgets"));

        $config = Config::getInstance();
        $var = new TypeString("video", "webm", $config, false, false);
        $var->setDefaultValue("http://reaby.kapsi.fi/ml/go2.webm");
        $this->registerVariable($var);
    }
}
