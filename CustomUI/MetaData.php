<?php

namespace ManiaLivePlugins\eXpansion\CustomUI;

use ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeFloat;

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
        $this->setName("Tools: Game UI Elements");
        $this->setDescription("Enables you to showw/hide ingame hud elements");
        $this->setGroups(array('Tools'));

        $config = Config::getInstance();

        $var = new Boolean("overlayHideNotices", "Hide Notices", $config, false, false);
        $var->setDefaultValue(false);
        $this->registerVariable($var);

        $var = new Boolean("overlayHideMapInfo", "Hide Map Info", $config, false, false);
        $var->setDefaultValue(false);
        $this->registerVariable($var);

        $var = new Boolean("overlayHideMultilapInfos", "Hide Multilap Info", $config, false, false);
        $var->setDefaultValue(false);
        $this->registerVariable($var);

        $var = new Boolean("overlayHideOpponentsInfo", "Hide Opponents Info", $config, false, false);
        $var->setDefaultValue(false);
        $this->registerVariable($var);

        $var = new Boolean("overlayHideChat", "Hide Chatbox", $config, false, false);
        $var->setDefaultValue(false);
        $this->registerVariable($var);

        $var = new Boolean("overlayHideCheckPointList", "Hide CheckPoint List", $config, false, false);
        $var->setDefaultValue(false);
        $this->registerVariable($var);

        $var = new Boolean("overlayHideRoundScores", "Hide Round Scores", $config, false, false);
        $var->setDefaultValue(false);
        $this->registerVariable($var);

        $var = new Boolean("overlayHideCountdown", "Hide Countdown", $config, false, false);
        $var->setDefaultValue(false);
        $this->registerVariable($var);

        $var = new Boolean("overlayHideCrosshair", "Hide Crosshair", $config, false, false);
        $var->setDefaultValue(false);
        $this->registerVariable($var);

        $var = new Boolean("overlayHideGauges", "Hide Gauges", $config, false, false);
        $var->setDefaultValue(false);
        $this->registerVariable($var);

        $var = new Boolean("overlayHideConsumables", "Hide Consumables", $config, false, false);
        $var->setDefaultValue(false);
        $this->registerVariable($var);

        $var = new Boolean("overlayHide321Go", "Hide 321Go", $config, false, false);
        $var->setDefaultValue(false);
        $this->registerVariable($var);

        $var = new Boolean("overlayHideChrono", "Hide Chrono", $config, false, false);
        $var->setDefaultValue(false);
        $this->registerVariable($var);

        $var = new Boolean("overlayHideSpeedAndDist", "Hide Speed And Dist", $config, false, false);
        $var->setDefaultValue(false);
        $this->registerVariable($var);

        $var = new Boolean("overlayHidePersonnalBestAndRank", "Hide PersonnalBest And Rank", $config, false, false);
        $var->setDefaultValue(false);
        $this->registerVariable($var);

        $var = new Boolean("overlayHidePosition", "Hide Position", $config, false, false);
        $var->setDefaultValue(false);
        $this->registerVariable($var);

        $var = new Boolean("overlayHideCheckPointTime", "Hide CheckPoint Time", $config, false, false);
        $var->setDefaultValue(false);
        $this->registerVariable($var);

        $var = new Boolean("overlayHideBackground", "Hide Background", $config, false, false);
        $var->setDefaultValue(false);
        $this->registerVariable($var);

        $var = new Boolean("overlayHideEndMapLadderRecap", "Hide End Map Ladder Recap", $config, false, false);
        $var->setDefaultValue(false);
        $this->registerVariable($var);

        $var = new Boolean("overlayChatHideAvatar", "Hide Chat Avatar", $config, false, false);
        $var->setDefaultValue(false);
        $this->registerVariable($var);

        $var = new BasicList("overlayChatOffset", "Chat Offset", $config, false, false);
        $var->setDescription("x=0.0 to 3.2, y=0 to 1.8");
        $var->setType(new TypeFloat(''));
        $var->setDefaultValue(array(0, 0));
        $this->registerVariable($var);

        $var = new BasicList("countdownCoord", "Timer Coordinates", $config, false, false);
        $var->setType(new TypeFloat(''));
        $var->setDefaultValue(array(0, -85.));
        $this->registerVariable($var);

        $var = new \ManiaLivePlugins\eXpansion\Core\types\config\types\TypeInt(
            "overlayChatLineCount",
            "Chat Line Count",
            $config,
            false,
            false
        );
        $var->setDefaultValue(7);
        $this->registerVariable($var);
    }

}
