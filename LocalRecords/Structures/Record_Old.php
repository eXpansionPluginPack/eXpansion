<?php
namespace ManiaLivePlugins\eXpansion\LocalRecords\Structures;

class Record2 extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure
{

    public $login;
    public $time;
    public $place = -1;


    public function __construct($login, $time, $place = -1)
    {
        $this->login = $login;
        $this->time = $time;
        $this->place = $place;
    }

}
