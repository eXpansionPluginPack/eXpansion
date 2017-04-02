<?php

namespace ManiaLivePlugins\eXpansion\ExtendTime;

use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedTypeFloat;
use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeInt;
use Maniaplanet\DedicatedServer\Structures\GameInfos;

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
        $this->setName("Extend Time");

        $this->addGameModeCompability(GameInfos::GAMEMODE_TIMEATTACK);
        $this->addTitleSupport("TM");
        $this->addTitleSupport("Trackmania");

        $this->setDescription("Provides Votes to Extend timelimit on  a map");
        $this->setGroups(array('Tools'));

        $config = Config::getInstance();
        $var = new TypeInt("timelimit", "Default timelimit to set", $config, false, false);
        $var->setDefaultValue(300);
        $this->registerVariable($var);

        $config = Config::getInstance();
        $var = new BoundedTypeFloat("ratio", "voteRatio", $config, false, false);
        $var->setMax(1.0);
        $var->setMax(0.0);
        $var->setDefaultValue(0.49);
        $this->registerVariable($var);

    }
}
