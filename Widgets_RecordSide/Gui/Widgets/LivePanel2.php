<?php

namespace ManiaLivePlugins\eXpansion\Widgets_RecordSide\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Widgets_RecordSide\Gui\Controls\Recorditem;
use ManiaLivePlugins\eXpansion\Widgets_RecordSide\Widgets_RecordSide;

class LivePanel2 extends LivePanel {

    
     function onConstruct() {
        parent::onConstruct();
        $this->setName("Live Rankings Panel (tab-layer)");
	$this->timeScript->setParam('varName','LiveTime2');
    }
    
}

?>
