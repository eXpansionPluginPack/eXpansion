<?php

namespace ManiaLivePlugins\eXpansion\Gui\Scripts;

/**
 * Description of ButtonScript
 *
 * @author De Cramer Oliver
 */
class ButtonScript extends \ManiaLivePlugins\eXpansion\Gui\Structures\Script{
    
    private $min = 999999;
    private $max = 0;
    
    function __construct() {
        parent::__construct("Gui/Scripts/Button");
    }

    
    public function getDeclarationScript($id, $component){  
        $decl = $component->getDescription();
        if(!empty($decl)){
            if($this->max < $component->getButtonId())
                $this->max = $component->getButtonId();
            if($this->min > $component->getButtonId())
                $this->min = $component->getButtonId();
        }
        
        return parent::getDeclarationScript($id, $component);
    }
    
    public function reset(){
        $this->min = 999999;
        $this->max = 0;
    }
}

?>
