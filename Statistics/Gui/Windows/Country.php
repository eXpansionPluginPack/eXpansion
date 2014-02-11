<?php

namespace ManiaLivePlugins\eXpansion\Statistics\Gui\Windows;

class Country extends StatsWindow {

    public static $labelTitles = array('#', 'Country', 'nb Players');

    protected function getKeys() {
	return array(null, 'nation', 'nb');
    }

    protected function getLabel($i) {
	return isset(self::$labelTitles[$i]) ? self::$labelTitles[$i] : "";
    }

    protected function getWidths() {
	return array(1, 5, 3);
    }

    protected function getFormaters() {
	return array(null, \ManiaLivePlugins\eXpansion\Gui\Formaters\Country::getInstance(), null);
    }

    public function populateList($list) {

	$newData = array();
	foreach ($list as $data) {
	    $sum = 0;
	    $formatter = \ManiaLivePlugins\eXpansion\Gui\Formaters\Country::getInstance();
	    $country = $formatter->format($data['nation']);
	    if($country != ""){
		if (isset($newData[$country])) {
		    $newData[$country]['nb'] += $data['nb'];
		} else {
		    $newData[$country] = $data;
		}
	    }
	}

	usort($newData, array($this, "cmp"));
	
	parent::populateList($newData);
    }

    public function cmp($a, $b) {
	if ($a['nb'] == $b['nb']) {
	    return 0;
	}
	return ($a['nb'] > $b['nb']) ? -1 : 1;
    }

}

?>
