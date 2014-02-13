<?php

namespace ManiaLivePlugins\eXpansion\Widgets_RecordSide\Gui\Widgets;


class DediPanel2 extends DediPanel {
     function onConstruct() {

	$this->setName("Dedimania Panel (Tab-layer)");
	parent::onConstruct();
	$this->timeScript->setParam('varName','DediTime2');
     }
}
?>
