<?php

namespace ManiaLivePlugins\eXpansion\ESportsManager\Structures;

/**
 * Description of MatchSettings
 *
 * @author Reaby
 */
class MatchSetting extends \DedicatedApi\Structures\AbstractStructure {

    public $matchTitle = "Not available";
    public $matchOrganizer = "Esl";
    public $rulesText = "This is failsafe text";
    public $rulesUrl = "";
    public $gameMode = -1;

    /** @var DedicatedApi\Structures\GameInfos */
    public $gameInfos = array();
    
    public $adminCommands = array();

}
