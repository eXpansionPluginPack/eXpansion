<?php

namespace ManiaLivePlugins\eXpansion\Core\types\config\types;

/**
 * Description of String
 *
 * @author De Cramer Oliver
 */
class ColorCode extends String{
    
    private $sample = "Sample Text ...";
    
    public function getSample() {
	return $this->sample;
    }

    public function setSample($sample) {
	$this->sample = $sample;
    }

        
    public function getPreviewValues() {
	return  $this->getRawValue().$this->sample;
    }
    
}

?>
