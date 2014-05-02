<?php

namespace ManiaLivePlugins\eXpansion\Widgets_RecordSide\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Widgets_RecordSide\Gui\Controls\Recorditem;
use ManiaLivePlugins\eXpansion\Widgets_RecordSide\Widgets_RecordSide;

class DediPanel extends LocalPanel {

    function exp_onBeginConstruct() {
	parent::exp_onBeginConstruct();
	$this->setName("Dedimania Panel");
	$this->timeScript->setParam("acceptMinCp", 2);
	$this->timeScript->setParam('varName', 'DediTime1');
    }

    function update() {
	$this->timeScript->setParam("acceptMaxPlayerRank", \ManiaLivePlugins\eXpansion\Dedimania\Classes\Connection::$serverMaxRank);
	$login = $this->getRecipient();

	foreach ($this->items as $item)
	    $item->destroy();
	$this->items = array();
	$this->frame->clearComponents();

	$index = 1;

	$this->lbl_title->setText('Dedimania Records');


	$recsData = "";
	$nickData = "";

	for ($index = 1; $index <= $this->nbFields; $index++) {
	    $this->items[$index - 1] = new Recorditem($index, false);
	    $this->frame->addComponent($this->items[$index - 1]);
	}

	$index = 1;
	foreach (Widgets_RecordSide::$dedirecords as $record) {
	    if ($index > 1) {
		$recsData .= ', ';
		$nickData .= ', ';
	    }
	    $recsData .= '"' . $this->fixDashes($record['Login']) . '"=> ' . $record['Best'];
	    $nickData .= '"' . $this->fixDashes($record['Login']) . '"=>"' . $this->fixHyphens($record['NickName']) . '"';
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
	$this->timeScript->setParam("acceptMaxPlayerRank", "Integer[Text]");
	$this->timeScript->setParam("useMaxPlayerRank", "True");
	if (count(\ManiaLivePlugins\eXpansion\Dedimania\Classes\Connection::$players) > 0) {
	    $out = "[";
	    foreach (\ManiaLivePlugins\eXpansion\Dedimania\Classes\Connection::$players as $dediplayer) {
		$out .= '"' . $dediplayer->login . '" => ' . $dediplayer->maxRank . ',';
	    }
	    $out = trim($out, ",");
	    $out = $out . "]";

	    $this->timeScript->setParam("acceptMaxPlayerRank", $out);
	}
    }

    function fixDashes($string) {
	$out = str_replace('--', '––', $string);
	return $out;
    }

    function fixHyphens($string) {
	$out = str_replace('"', "'", $string);
	$out = str_replace('\\', '\\\\', $out);
	$out = str_replace('-', '–', $out);
	return $out;
    }

}

?>
