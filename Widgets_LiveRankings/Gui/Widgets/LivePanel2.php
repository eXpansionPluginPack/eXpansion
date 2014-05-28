<?php

namespace ManiaLivePlugins\eXpansion\Widgets_LiveRankings\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Widgets_LocalRecords\Gui\Controls\Recorditem;
use ManiaLivePlugins\eXpansion\Widgets_LocalRecords\Widgets_RecordSide;

class LivePanel2 extends LivePanel {

    function exp_onBeginConstruct() {
	parent::exp_onBeginConstruct();
	// $this->setName("Live Rankings Panel (Tab-layer)");
	$this->timeScript->setParam('varName', 'LiveTime2');
    }

}

?>
