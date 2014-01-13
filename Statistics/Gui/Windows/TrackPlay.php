<?php

namespace ManiaLivePlugins\eXpansion\Statistics\Gui\Windows;



class TrackPlay extends StatsWindow {

    public static $labelTitles = array('#','NickName', 'nb Maps');
    
    protected function getKeys() {
        return array(0, 'nickname', 'nb');
    }

    protected function getLabel($i) {
        return isset(self::$labelTitles[$i]) ? self::$labelTitles[$i] : "";
    }

    protected function getWidths() {
        return array(1, 5, 3);
    }

}

?>
