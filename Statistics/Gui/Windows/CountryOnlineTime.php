<?php

namespace ManiaLivePlugins\eXpansion\Statistics\Gui\Windows;


class CountryOnlineTime extends Country
{

    public static $labelTitles = array('#', 'Country', 'Time Online');

    protected function getKeys()
    {
        return array(null, 'nation', 'time');
    }

    protected function getLabel($i)
    {
        return isset(self::$labelTitles[$i]) ? self::$labelTitles[$i] : "";
    }

    protected function getWidths()
    {
        return array(1, 5, 3);
    }

    protected function getFormaters()
    {
        return array(null, \ManiaLivePlugins\eXpansion\Gui\Formaters\Country::getInstance()
        , \ManiaLivePlugins\eXpansion\Gui\Formaters\LongDate::getInstance());
    }

    public function populateList($list)
    {

        $newData = array();
        foreach ($list as $data) {
            $sum = 0;
            $formatter = \ManiaLivePlugins\eXpansion\Gui\Formaters\Country::getInstance();
            $country = $formatter->format($data['nation']);
            if ($country != "") {
                if (isset($newData[$country])) {
                    $newData[$country]['time'] += $data['time'];
                } else {
                    $newData[$country] = $data;
                }
            }
        }

        usort($newData, array($this, "cmp"));

        parent::populateList($newData);
    }

    public function cmp($a, $b)
    {
        if ($a['time'] == $b['time']) {
            return 0;
        }

        return ($a['time'] > $b['time']) ? -1 : 1;
    }

}
