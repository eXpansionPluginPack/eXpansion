<?php

namespace ManiaLivePlugins\eXpansion\Widgets_RecordSide\Gui\Widgets;


class DediPanel2 extends DediPanel {
    
     function exp_onBeginConstruct() {
	// $this->setName("Dedimania Panel (Tab-layer)");	
	parent::exp_onBeginConstruct();
	
	$this->timeScript->setParam('varName','DediTime2');
     }
}
?>
