<?php

namespace ManiaLivePlugins\eXpansion\Dedimania\Structures;

class DediRecord extends \DedicatedApi\Structures\AbstractStructure {

    public $login;
    public $time;
    public $nickname;
    public $place = -1;
    public $checkpoints = "";

    public function __construct($login, $nickname, $time, $place = -1, $checkpoints = "") {
        $this->login = $login;
        $this->time = $time;
        $this->nickname = $nickname;
        $this->place = $place;
        $this->checkpoints = $checkpoints;
        if (is_array($checkpoints))
            $this->checkpoints = implode(",", $checkpoints);
    }

}

?>
