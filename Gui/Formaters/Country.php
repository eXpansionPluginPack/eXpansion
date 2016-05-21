<?php

namespace ManiaLivePlugins\eXpansion\Gui\Formaters;

/**
 * Description of AbstractFormater
 *
 * @author De Cramer Oliver
 */
class Country extends AbstractFormater
{

    public function format($val)
    {
        $vals = explode('|', $val);
        if (isset($vals[2]))
            return $vals[2];
        else
            return "";
    }
}
