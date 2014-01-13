<?php

namespace ManiaLivePlugins\eXpansion\Statistics\Gui\Windows;



class CountryOnlineTime extends StatsWindow {

    public static $labelTitles = array('#','Country', 'Time Online');
    
    protected function getKeys() {
        return array(0, 'nation', 'time');
    }

    protected function getLabel($i) {
        return isset(self::$labelTitles[$i]) ? self::$labelTitles[$i] : "";
    }

    protected function getWidths() {
        return array(1, 5, 3);
    }
    
    protected function getFormaters() {
        return array(null, \ManiaLivePlugins\eXpansion\Gui\Formaters\Country::getInstance()
                , \ManiaLivePlugins\eXpansion\Gui\Formaters\LongDate::getInstance());
    }

}

?>
