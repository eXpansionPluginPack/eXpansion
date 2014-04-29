<?php

namespace ManiaLivePlugins\eXpansion\Core\types\config\types;
/**
 * Description of Int
 *
 * @author De Cramer Oliver
 */
class Float extends \ManiaLivePlugins\eXpansion\Core\types\config\Variable{
   
    public function setValue($value){
	if($this->basicValueCheck($value))
	    return $this->setRawValue ($value);

	return false;
    }
    
    public function basicValueCheck($value){
	return parent::basicValueCheck($value) && is_float($value);
    }
    
    public function getPreviewValues() {
	return  $this->getRawValue();
    }
}

?>
