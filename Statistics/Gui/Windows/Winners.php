<?php

namespace ManiaLivePlugins\eXpansion\Statistics\Gui\Windows;


class Winners extends StatsWindow
{

    public static $labelTitles = array('#', 'NickName', 'nb Wins');

    protected function getKeys()
    {
        return array(0, 'nickname', 'wins');
    }

    protected function getLabel($i)
    {
        return isset(self::$labelTitles[$i]) ? self::$labelTitles[$i] : "";
    }

    protected function getWidths()
    {
        return array(1, 5, 3);
    }

}
