<?php

namespace ManiaLivePlugins\eXpansion\Widgets_CombinedRecords\Gui\Widgets;

class CombiPanel2 extends PlainPanel
{
    public function eXpOnBeginConstruct()
    {
        // $this->setName("Dedimania Panel (Tab-layer)");
        parent::eXpOnBeginConstruct();

        $this->timeScript->setParam('varName', 'CombiTime2');
    }
}
