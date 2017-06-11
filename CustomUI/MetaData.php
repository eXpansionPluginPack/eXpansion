<?php

namespace ManiaLivePlugins\eXpansion\CustomUI;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;


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
        $this->setName("Game UI Elements");
        $this->setDescription("Enables you to show/hide ingame hud elements");
        $this->setGroups(array('Tools'));

        $config = Config::getInstance();

        $var = new Boolean("notices", "Notices", $config, false, false);
        $var->setDefaultValue(true);
        $this->registerVariable($var);

        $var = new Boolean("live_info", "live_info", $config, false, false);
        $var->setDefaultValue(true);
        $this->registerVariable($var);

        $var = new Boolean("map_info", "map_info", $config, false, false);
        $var->setDefaultValue(true);
        $this->registerVariable($var);

        $var = new Boolean("chat", "chat", $config, false, false);
        $var->setDefaultValue(true);
        $this->registerVariable($var);

        $var = new Boolean("countdown", "countdown", $config, false, false);
        $var->setDefaultValue(true);
        $this->registerVariable($var);

        $var = new Boolean("crosshair", "crosshair", $config, false, false);
        $var->setDefaultValue(true);
        $this->registerVariable($var);

        $var = new Boolean("gauges", "gauges", $config, false, false);
        $var->setDefaultValue(true);
        $this->registerVariable($var);

        $var = new Boolean("consumables", "consumables", $config, false, false);
        $var->setDefaultValue(true);
        $this->registerVariable($var);

        $var = new Boolean("go", "go", $config, false, false);
        $var->setDefaultValue(true);
        $this->registerVariable($var);

        $var = new Boolean("chat_avatar", "chat_avatar", $config, false, false);
        $var->setDefaultValue(true);
        $this->registerVariable($var);

        $var = new Boolean("endmap_ladder_recap", "endmap_ladder_recap", $config, false, false);
        $var->setDefaultValue(true);
        $this->registerVariable($var);

    }
}
