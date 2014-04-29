<?php

namespace ManiaLivePlugins\eXpansion\Core\types\config\types;
/**
 * Description of Int
 *
 * @author De Cramer Oliver
 */
class Boolean extends \ManiaLivePlugins\eXpansion\Core\types\config\Variable{
   
    
    private $labelTrue = "True";
    private $labelFalse = "True";
    
    public function setValue($value){
	if($this->basicValueCheck($value))
	    return $this->setRawValue ($value);

	return false;
    }
    
    public function basicValueCheck($value){
	return parent::basicValueCheck($value) && is_bool($value);
    }
    
    public function getLabelTrue() {
	return $this->labelTrue;
    }

    public function getLabelFalse() {
	return $this->labelFalse;
    }

    public function setLabelTrue($labelTrue) {
	$this->labelTrue = $labelTrue;
    }

    public function setLabelFalse($labelFAlse) {
	$this->labelFalse = $labelFAlse;
    }

    public function getPreviewValues() {
	return $this->getRawValue() ? $this->labelTrue : $this->labelFalse;
    }

}

?>
