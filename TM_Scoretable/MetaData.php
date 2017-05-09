<?php

namespace ManiaLivePlugins\eXpansion\TM_Scoretable;

use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedTypeInt;
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
        $this->setName("Customize Scores table");
        $this->setDescription("Customizes the scoretable for scripted modes");
        $this->setGroups(array('Tools'));

        $this->addTitleSupport("TM");
        $this->addTitleSupport("Trackmania");
        $this->setEnviAsTitle(false);

        $this->addGameModeCompability(GameInfos::GAMEMODE_SCRIPT, "TimeAttack.Script.txt");
        $this->setScriptCompatibilityMode(false);

        $this->setRelaySupport(true);

        $config = Config::getInstance();


        $var = new BoundedTypeInt("tm_score_columns", "Scoretable columns number", $config, false, false);
        $var->setMax(10);
        $var->setMin(2);
        $var->setDefaultValue(2);
        $this->registerVariable($var);

        $var = new BoundedTypeInt("tm_score_lines", "Lines per column", $config, false, false);
        $var->setMax(20);
        $var->setMin(5);
        $var->setDefaultValue(8);
        $this->registerVariable($var);
    }
}
