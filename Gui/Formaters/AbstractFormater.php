<?php

namespace ManiaLivePlugins\eXpansion\Gui\Formaters;

/**
 * Description of AbstractFormater
 *
 * @author De Cramer Oliver
 */
abstract class AbstractFormater extends \ManiaLib\Utils\Singleton{
    
    abstract public function format($val);

}

?>
