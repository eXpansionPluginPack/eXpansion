<?php

namespace ManiaLivePlugins\eXpansion\Dedimania\Structures;

class DediPlayer extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure {

    /** @var string */
    public $login;

    /** @var int */
    public $maxRank = 15;

    /** @var bool */
    public $banned = false;

    /** @var bool */
    public $optionsEnabled = false;

    /** @var string */
    public $toolOption = "";

}

?>
