<?php

namespace ManiaLivePlugins\eXpansion\InfoMessage;

use ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\ColorCode;
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
        $this->setName("Info Messages");
        $this->setDescription("Send informatic chat message every now and then");
        $this->setGroups(array('Tools', 'Chat'));

        $config = Config::getInstance();

        $var = new BasicList("infoMessages", "Messages", $config, false, false);
        $var->setType(new TypeString(""));
        $var->setDefaultValue(array());
        $this->registerVariable($var);

        $var = new TypeString("infoInterval", "Interval in mm:ss", $config, false, false);
        $var->setDefaultValue("1:00");
        $this->registerVariable($var);

        $var = new ColorCode("infoMessageColor", "Color for message", $config, false, false);
        $var->setDefaultValue('$fff');
        $this->registerVariable($var);
    }
}
