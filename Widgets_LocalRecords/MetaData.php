<?php

namespace ManiaLivePlugins\eXpansion\Widgets_LocalRecords;

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
        $this->setName("Widget: Local Records");
        $this->setDescription("Local Records widget");
        $this->setGroups(array('Widgets', 'Records'));

        $this->addTitleSupport("TM");
        $this->addTitleSupport("Trackmania");
        $this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_ROUNDS);
        $this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK);
        $this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM);
        $this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS);
        $this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP);
        $this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT, 'TeamAttack.Script.txt');

        $config = Config::getInstance();
        $var = new Boolean("isHorizontal", "Use horizontal (old) widget style", $config, false, false);
        $var->setDefaultValue(false);
        $this->registerVariable($var);
    }
}
