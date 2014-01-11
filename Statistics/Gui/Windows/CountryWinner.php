<?php

namespace ManiaLivePlugins\eXpansion\Statistics\Gui\Windows;



class CountryWinner extends \ManiaLivePlugins\eXpansion\Gui\Windows\PagerWindow {

    public static $labelTitles = array('#','Country', 'nb Finish');
    
    protected function getKeys() {
        return array(0, 'nation', 'nb');
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
}

?>
