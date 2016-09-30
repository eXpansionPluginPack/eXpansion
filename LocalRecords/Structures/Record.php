<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords\Structures;

class Record
{

    public $isNew = false;
    public $isUpdated = false;
    public $isDelete = false;
    public $place;
    public $login;
    public $nickName;
    public $time;
    public $nbFinish;
    public $nbWins;
    public $avgScore;

    /** @var int[] */
    public $ScoreCheckpoints = array();
    public $date;
    public $nation;
    public $uId;
}
