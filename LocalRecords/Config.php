<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords;

class Config extends \ManiaLib\Utils\Singleton {


    public $sendBeginMapNotices = true;
    public $sendRankingNotices = true;
    public $recordsCount = 30;

    public $lapsModeCount1lap = true;

    public $nbMap_rankProcess = 5;
    public $totalRankProcessCoef = 20;

    public $msg_secure = '#variable#%1$s #record#secured his/her #rank#%2$s #record#. Local Record with time of #rank#%3$s #record#$n(-%5$s)';
    public $msg_new = '#variable#%1$s #record#gained the #rank#%2$s #record#. Local Record with time of #rank#%3$s';
    public $msg_newMap = '#variable#%1$s #record#Is a new Map. Currently no record!';
    public $msg_BeginMap = '#record#Current record on #variable#%1$s #record#is #variable#%2$s #record#by #variable#%3$s';
    public $msg_showRank = '#record#Server rank: #variable#%1$s#record#/#variable#%2$s';
    public $msg_noRank = '#admin_error#$iNot enough local records to obtain ranking yet..';

}
?>
