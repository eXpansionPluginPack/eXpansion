<?php

namespace ManiaLivePlugins\eXpansion\MXKarma;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
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
        $this->setName("Maps: MX-karma");
        $this->setDescription("Provides integration for Karma.Mania-Exchange.com");
        $this->setGroups(array('Maps', 'Connectivity'));

        $config = Config::getInstance();

        $var = new TypeString("mxKarmaServerLogin", "MxKarma serverlogin", $config, false, false);
        $var->setDefaultValue("");
        $this->registerVariable($var);

        $var = new TypeString("mxKarmaApiKey", 'MxKarma apikey, $l[http://karma.mania-exchange.com]click this text to register$l', $config, false, false);
        $var->setDescription('For apikey: click the header or visit http://karma.mania-exchange.com');
        $var->setDefaultValue("");
        $this->registerVariable($var);

        $this->setRelaySupport(false);
    }

}
