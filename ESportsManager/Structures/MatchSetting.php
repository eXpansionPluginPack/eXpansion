<?php

namespace ManiaLivePlugins\eXpansion\ESportsManager\Structures;

/**
 * Description of MatchSettings
 *
 * @author Reaby
 */
class MatchSetting extends \DedicatedApi\Structures\AbstractStructure {
   
    
    public $matchTitle = "Not available";
    public $matchOrganizer = "";
    
    public $rulesText = "This is failsafe text";
    public $rulesUrl = "";
    
    public $gameMode = -1;
    
    public $adminCommands = array();
    
    
}
