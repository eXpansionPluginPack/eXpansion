<?php

namespace ManiaLivePlugins\eXpansion\Widgets_DedimaniaRecords\Gui\Widgets;


class DediPanel2 extends PlainPanel
{

    function eXpOnBeginConstruct()
    {
        // $this->setName("Dedimania Panel (Tab-layer)");
        parent::eXpOnBeginConstruct();

        $this->timeScript->setParam('varName', 'DediTime2');
    }
}

?>
