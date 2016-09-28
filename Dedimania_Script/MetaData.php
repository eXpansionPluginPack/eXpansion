<?php

namespace ManiaLivePlugins\eXpansion\Dedimania_Script;

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
        $this->setName("Records: Dedimania for Scripted modes");
        $this->setDescription("Dedimania, Global world records system integration");
        $this->setGroups(array('Records'));

        $this->addTitleSupport("TM");
        $this->addTitleSupport("Trackmania");
        $this->setEnviAsTitle(true);

        $this->addGameModeCompability(
            \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT,
            "TimeAttack.Script.txt"
        );
        $this->addGameModeCompability(
            \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT,
            "Rounds.Script.txt"
        );
        $this->addGameModeCompability(
            \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT,
            "Cup.Script.txt"
        );
        $this->addGameModeCompability(
            \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT,
            "Team.Script.txt"
        );
        $this->addGameModeCompability(
            \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT,
            "Laps.Script.txt"
        );
        $this->setScriptCompatibilityMode(false);

        $config = \ManiaLivePlugins\eXpansion\Dedimania\Config::getInstance();

        $var = new TypeString("login", "Dedimania server login (use this server login)", $config, false, false);
        $var->setDefaultValue("");
        $this->registerVariable($var);

        $var = new TypeString(
            "code",
            'Dedimania $l[http://dedimania.net/tm2stats/?do=register]server code$l',
            $config,
            false,
            false
        );
        $var->setDefaultValue("");
        $this->registerVariable($var);

        $this->setRelaySupport(false);
    }
}
