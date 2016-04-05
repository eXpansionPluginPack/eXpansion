<?php

namespace ManiaLivePlugins\eXpansion\Widgets_LiveRankings\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Widgets_LocalRecords\Widgets_RecordSide;

class LivePanel2 extends PlainLivePanel
{

    function eXpOnBeginConstruct()
    {
        parent::eXpOnBeginConstruct();
        // $this->setName("Live Rankings Panel (Tab-layer)");
        $this->timeScript->setParam('varName', 'LiveTime2');
    }

}

?>
