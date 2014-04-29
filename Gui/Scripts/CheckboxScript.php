<?php

namespace ManiaLivePlugins\eXpansion\Gui\Scripts;

/**
 * Description of ButtonScript
 *
 * @author De Cramer Oliver
 */
class CheckboxScript extends \ManiaLivePlugins\eXpansion\Gui\Structures\Script{
    
    private $putEntry = false;
    
    function __construct() {
        parent::__construct("Gui/Scripts/Checkbox");
    }   
    
    public function reset() {
	parent::reset();
    }
}

?>
