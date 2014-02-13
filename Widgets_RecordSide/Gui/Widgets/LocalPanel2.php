<?php

namespace ManiaLivePlugins\eXpansion\Widgets_RecordSide\Gui\Widgets;

class LocalPanel2 extends LocalPanel {

    protected function onConstruct() {
	
	$this->setName("LocalRecords Panel (Tab-layer)");
	parent::onConstruct();
	$this->timeScript->setParam('varName','LocalTime2');
    }
}

?>