<?php

namespace ManiaLivePlugins\eXpansion\Widgets_CombinedRecords;

use ManiaLive\PluginHandler\PluginHandler;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedTypeInt;

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
        $this->setName("Widget: Combined Records");
        $this->setDescription("Provides dedimania records widget");
        $this->setGroups(array('Widgets', 'Records'));

        $this->addTitleSupport("TM");
        $this->addTitleSupport("Trackmania");
        $this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_ROUNDS);
        $this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK);
        $this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM);
        $this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS);
        $this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP);
        $this->addGameModeCompability(
            \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT,
            'TeamAttack.Script.txt'
        );

        $config = Config::getInstance();

        $var = new BoundedTypeInt("nbTop", "Number of top records (always shown)", $config, false, false);
        $var->setMin(1);
        $var->setMax(20);
        $var->setDefaultValue(5);
        $this->registerVariable($var);

        $var = new BoundedTypeInt("nbTotal", "Number of records displayed", $config, false, false);
        $var->setMin(1);
        $var->setMax(30);
        $var->setDefaultValue(15);
        $this->registerVariable($var);

        $var = new Boolean("isHorizontal", "Use horizontal (old) widget style", $config, false, false);
        $var->setDefaultValue(true);
        $this->registerVariable($var);
    }


    public function checkOtherCompatibility()
    {
        $errors = parent::checkOtherCompatibility();

        $dedi1 = '\ManiaLivePlugins\\eXpansion\\Dedimania\\Dedimania';
        $dedi2 = '\ManiaLivePlugins\\eXpansion\\Dedimania_Script\\Dedimania_Script';

        /** @var PluginHandler $phandler */
        $phandler = PluginHandler::getInstance();

        if ($phandler->isLoaded($dedi1)) {
            return $errors;
        } elseif ($phandler->isLoaded($dedi2)) {
            return $errors;
        }

        $errors[] = 'Dedimania Records Panel needs a running Dedimania plugin!!';

        return $errors;
    }
}
