<?php

namespace ManiaLivePlugins\eXpansion\Maps;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\SortedList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeInt;

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
        $this->setName("Maps: Generic Management and Jukebox");
        $this->setDescription("Provides common functions for maps, add, remove, list and jukebox");
        $this->setGroups(array('Maps'));

        $config = Config::getInstance();

        $var = new SortedList("publicQueueAmount", "Jukeboxing costs", $config, false, true);
        $var->setGroup('Planets');
        $var->setType(new TypeInt("", "", null));
        $var->setDefaultValue(array(0));
        $this->registerVariable($var);

        $var = new TypeInt("bufferSize", "Set jukebox total size", $config, false, false);
        $var->setGroup("Buffers");
        $var->setDefaultValue(5);
        $this->registerVariable($var);


        $var = new TypeInt("historySize", "How many recent maps players can't jukebox", $config, false, false);
        $var->setDescription('Will be shown color red at maps list. Note: Admins can jukebox every map anyway');
        $var->setGroup("Buffers");
        $var->setDefaultValue(7);
        $this->registerVariable($var);

        $var = new Boolean("showCurrentMapWidget", "Show Now Playing map widget", $config, false, false);
        $var->setGroup("Widgets");
        $var->setDefaultValue(false);
        $this->registerVariable($var);

        $var = new Boolean("showNextMapWidget", "Show next map widget", $config, false, false);
        $var->setGroup("Widgets");
        $var->setDefaultValue(false);
        $this->registerVariable($var);

        $var = new Boolean("showEndMatchNotices", "Show end map notices", $config, false, false);
        $var->setGroup("Chat Messages");
        $var->setDefaultValue(false);
        $this->registerVariable($var);
    }
}
