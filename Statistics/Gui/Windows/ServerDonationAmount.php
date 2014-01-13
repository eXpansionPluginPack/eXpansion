<?php

namespace ManiaLivePlugins\eXpansion\Statistics\Gui\Windows;



class ServerDonationAmount extends StatsWindow {

    public static $labelTitles = array('#','NickName', 'Amount of Planets');
    
    protected function getKeys() {
        return array(0, 'nickname', 'totalPlanets');
    }

    protected function getLabel($i) {
        return isset(self::$labelTitles[$i]) ? self::$labelTitles[$i] : "";
    }

    protected function getWidths() {
        return array(1, 5, 3);
    }

}

?>
