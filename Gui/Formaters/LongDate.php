<?php

namespace ManiaLivePlugins\eXpansion\Gui\Formaters;

/**
 * Description of AbstractFormater
 *
 * @author De Cramer Oliver
 */
class LongDate extends AbstractFormater
{

    public function format($val)
    {
        $minutes = (int)(($val / 60) % 60);
        $hours = (int)(($val / 3600) % 24);
        $days = (int)(($val / (3600 * 24)) % 30);
        $text = $days . 'd ' . $hours . 'h ' . $minutes . 'min';

        $month = (int)(($val / (3600 * 24 * 30)));
        if ($month > 0) {
            $text = ($month % 30) . 'm ' . $days . 'd ' . $hours . 'h ';
        }

        $year = (int)(($val / (3600 * 24 * 30 * 12)));
        if ($year > 0) {
            $text = $year . 'y ' . ($month % 30) . 'm ' . $days . 'd ';
        }

        return $text;
    }
}
