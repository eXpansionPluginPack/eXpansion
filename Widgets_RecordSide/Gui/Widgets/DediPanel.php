<?php

namespace ManiaLivePlugins\eXpansion\Widgets_RecordSide\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Widgets_RecordSide\Gui\Controls\DediItem;
use ManiaLivePlugins\eXpansion\Widgets_RecordSide\Widgets_RecordSide;

class DediPanel extends LocalPanel {

    function onConstruct() {
        parent::onConstruct();
        $this->setName("Dedimania Panel");
    }
    
    function update() {
        $login = $this->getRecipient();
        $this->storage = \ManiaLive\Data\Storage::getInstance();
        foreach ($this->items as $item)
            $item->destroy();
        $this->items = array();
        $this->frame->clearComponents();

        $index = 1;

        $this->lbl_title->setText(__('Dedimania Records', $login));


        if (!is_array(Widgets_RecordSide::$dedirecords))
            return;
        foreach (Widgets_RecordSide::$dedirecords as $record) {
            if ($index > 30)
                return;
            $highlite = false;
            if (array_key_exists($record['Login'], $this->storage->players))
                $highlite = true;
            if (array_key_exists($record['Login'], $this->storage->spectators))
                $highlite = true;
            $this->items[$index - 1] = new DediItem($index, $record, $this->getRecipient(), $highlite);
            $this->frame->addComponent($this->items[$index - 1]);
            $index++;
        }
    }

}

?>
