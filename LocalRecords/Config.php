<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords;

class Config extends \ManiaLib\Utils\Singleton
{

    public $sendBeginMapNotices = false;  // show messages on beginmap
    public $sendRankingNotices = false; // show personal rank messages on beginmap
    public $recordsCount = 100; // number of records to save
    public $recordPublicMsgTreshold = 15; // records rank number to show public message
    public $lapsModeCount1lap = true;
    public $nbMap_rankProcess = 500;
    public $ranking = true;
    public $resetRanks = false;
    public $saveRecFrequency = 0;
    public $noRedirectTreshold = 30;
}
