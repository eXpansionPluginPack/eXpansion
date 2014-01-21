<?php

namespace ManiaLivePlugins\eXpansion\Gui\Scripts;

/**
 * Description of ButtonScript
 *
 * @author De Cramer Oliver
 */
class ButtonScript extends \ManiaLivePlugins\eXpansion\Gui\Structures\Script{
    
    public $min = 999999;
    public $max = 0;
    
    private $dec = false;
    private $loop = false;
    private $while = false;
    
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
        if(!$this->dec){
            $this->dec = true;
            return parent::getDeclarationScript($id, $component);
        }else
            return "";
    }
    
    public function getWhileLoopScript($id, $component){ 
        if(!$this->while){
            $this->while = true;
            return parent::getWhileLoopScript($id, $component);
        }else
            return "";
    }
    
    public function getMainLoopScript($id, $component){ 
        if(!$this->loop){
            $this->loop = true;
            return parent::getMainLoopScript($id, $component);
        }else
            return "";
    }
    
    public function reset(){
        $this->min = 999999;
        $this->max = 0;
        $this->dec = false;
        $this->loop = false;
        $this->while = false;
    }
    
    public function multiply() {
        return true;
    }
}

?>
