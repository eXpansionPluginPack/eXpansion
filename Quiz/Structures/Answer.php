<?php

namespace ManiaLivePlugins\eXpansion\Quiz\Structures;

class Answer extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure
{

    public $answer = null;
    public $value = 1.0;
    public $used = false;

    public function __construct($answer, $value = 1.0)
    {
        $this->answer = $answer;
        $this->value = $value;
    }
}
