<?php

namespace ManiaLivePlugins\eXpansion\Widgets_RecordSide\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Widgets_RecordSide\Gui\Controls\Recorditem;
use ManiaLivePlugins\eXpansion\Widgets_RecordSide\Widgets_RecordSide;

class DediPanel extends LocalPanel {

    function onConstruct() {
        parent::onConstruct();
        $this->setName("Dedimania Panel");
    }
    
    function update() {
       	$login = $this->getRecipient();
	foreach ($this->items as $item)
	    $item->destroy();
	$this->items = array();
	$this->frame->clearComponents();

	$index = 1;

	$this->lbl_title->setText(__('Dedimania Records', $login));


	$recsData = "";
	$nickData = "";

	for ($index = 1; $index <= 30; $index++) {
	    $this->items[$index - 1] = new Recorditem($index, false);
	    $this->frame->addComponent($this->items[$index - 1]);
	}

	$index = 1;
	foreach (Widgets_RecordSide::$dedirecords as $record) {
	    if ($index > 1) {
		$recsData .= ', ';
		$nickData .= ', ';
	    }
	    $recsData .= '"' . $record['Login'] . '"=> ' . $record['Best'];
	    $nickData .= '"' . $record['Login'] . '"=>"' . $this->fixHyphens($record['NickName']) . '"';
	    $index++;
	}
	$this->timeScript->setParam("totalCp", $this->storage->currentMap->nbCheckpoints);

	if (empty($recsData)) {
	    $recsData = 'Integer[Text]';
	    $nickData = 'Text[Text]';
	} else {
	    $recsData = '[' . $recsData . ']';
	    $nickData = '[' . $nickData . ']';
	}

	$this->timeScript->setParam("playerTimes", $recsData);
	$this->timeScript->setParam("playerNicks", $nickData);
    }

    function fixHyphens($string) {
	$out = str_replace('"', "'", $string);
	$out = str_replace("\'", "'", $out);
	return $out;
	
    }
}
 
?>
