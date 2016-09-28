<?php

namespace ManiaLivePlugins\eXpansion\MusicBox;

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
        $this->setName("Tools: Music Box");
        $this->setDescription("Provides custom musics loader for your server");
        $this->setGroups(array('Tools'));

        $config = Config::getInstance();

        $var = new Boolean(
            "override",
            "Override all music on server, even if map has defined custom one ?",
            $config,
            false,
            false
        );
        $var->setDefaultValue(true);
        $this->registerVariable($var);

        $var = new TypeString("url", "Enter tracklist index.csv url for musicbox ", $config, false, false);
        $var->setDefaultValue("http://reaby.kapsi.fi/ml/musictest");
        $this->registerVariable($var);

        $var = new Boolean("disableJukebox", "Disable jukeboxing of music?", $config, false, false);
        $var->setDefaultValue(false);
        $this->registerVariable($var);
    }
}
