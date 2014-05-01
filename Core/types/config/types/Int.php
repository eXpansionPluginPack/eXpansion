<?php

namespace ManiaLivePlugins\eXpansion\Core\types\config\types;

/**
 * Description of Int
 *
 * @author De Cramer Oliver
 */
class Int extends \ManiaLivePlugins\eXpansion\Core\types\config\Variable {

    public function setValue($value) {
	if ($this->basicValueCheck($value))
	    return $this->setRawValue((Int)$value);

	return false;
    }

    public function basicValueCheck($value) {
	return parent::basicValueCheck($value) && is_numeric($value) && ctype_digit((string)$value);
    }

    public function getPreviewValues() {
	return  $this->getRawValue();
    }
    
    public function castValue($value){
	return (int)$value;
    }

}

?>
