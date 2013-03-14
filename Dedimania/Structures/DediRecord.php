<?php
namespace ManiaLivePlugins\eXpansion\Dedimania\Structures;

class DediRecord extends \DedicatedApi\Structures\AbstractStructure {
    
    public $login;
    public $time;
    public $nickname;
    public $place = -1;
     
     
    public function __construct($login, $nickname, $time, $place = -1) {
        $this->login = $login;
        $this->time = $time;
        $this->nickname = $nickname;
        $this->place = $place;
    }
    
}
?>
