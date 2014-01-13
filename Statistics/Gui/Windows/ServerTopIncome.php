<?php

namespace ManiaLivePlugins\eXpansion\Statistics\Gui\Windows;



class ServerTopIncome extends StatsWindow {

    public static $labelTitles = array('#','extension', 'subject', 'Amount of Planets');
    
    protected function getKeys() {
        return array(0, 'plugin', 'subject','totalPlanets');
    }

    protected function getLabel($i) {
        return isset(self::$labelTitles[$i]) ? self::$labelTitles[$i] : "";
    }

    protected function getWidths() {
        return array(1, 5,5,3);
    }

}

?>
