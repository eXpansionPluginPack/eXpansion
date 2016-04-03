<?php

namespace ManiaLivePlugins\eXpansion\ESportsManager\Structures;

/**
 * Description of MatchSettings
 *
 * @author Reaby
 */
class MatchSetting extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure
{

    public $matchTitle = '';
    public $matchOrganizer = '';
    public $rulesText = '';
    public $rulesUrl = '';
    public $gameMode = -1;

    /** @var Maniaplanet\DedicatedServer\Structures\GameInfos */
    public $gameInfos = array();
    public $adminCommands = array();

}
