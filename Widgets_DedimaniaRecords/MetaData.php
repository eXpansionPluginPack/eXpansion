<?php

namespace ManiaLivePlugins\eXpansion\Widgets_DedimaniaRecords;

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
        $this->setName("Dedimania Records widgets");
        $this->setDescription("Provides dedimania records widget");

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
    }


    public function checkOtherCompatibility()
    {
        $dedi1 = '\ManiaLivePlugins\\eXpansion\\Dedimania\\Dedimania';
        $dedi2 = '\ManiaLivePlugins\\eXpansion\\Dedimania_Script\\Dedimania_Script';

        /** @var PluginHandler $phandler */
        $phandler = PluginHandler::getInstance();

        if ($phandler->isLoaded($dedi1) && $this->callPublicMethod($dedi2, 'isRunning')) {
            return true;
        } elseif ($phandler->isLoaded($dedi2)) {
            return true;
        }

        return false;
    }
}
