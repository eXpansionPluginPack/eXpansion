<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords\Structures;

class Record {
    
    public $isNew = false;
    public $isUpdated = false;
    
    public $place;
    public $login;
    public $nickName;
    public $time;
    public $nbFinish;
    public $avgScore;
    public $ScoreCheckpoints = array();
    public $date;

}

?>
