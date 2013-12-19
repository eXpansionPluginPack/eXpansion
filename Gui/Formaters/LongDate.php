<?php

namespace ManiaLivePlugins\eXpansion\Gui\Formaters;

/**
 * Description of AbstractFormater
 *
 * @author De Cramer Oliver
 */
class LongDate extends AbstractFormater{
    
    public function format($val){
        $minutes = (int)(($val/60)%60);
        $hours = (int)(($val/3600)%3600);
        $days = (int)(($val/(3600*24)));
        return $days.'d'.$hours.'h'.$minutes.'min';
    }
}

?>
