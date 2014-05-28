<?php

namespace ManiaLivePlugins\eXpansion\Widgets_LocalRecords\Gui\Widgets;

class LocalPanel2 extends LocalPanel {

    protected function exp_onBeginConstruct() {
	
	parent::exp_onBeginConstruct();
	// $this->setName("LocalRecords Panel (Tab-layer)");
	$this->timeScript->setParam('varName','LocalTime2');
    }
}

?>