<?php

namespace ManiaLivePlugins\eXpansion\Gui\Structures;

/**
 * Description of Script
 *
 * @author De Cramer Oliver
 */
class Script {
    
    private $_relPath = "";
    
    function __construct($relPath) {
        $this->_relPath = __DIR__.'/../../'.$relPath;
    }

    public function getRelPath() {
        return $this->_relPath;
    }

    public function setRelPath($relPath) {
        $this->_relPath = $relPath;
    }

    public function setParam($name, $value){
        $this->$name = $value;
    }
    
    public function getDeclarationScript($id, $component){
        return $this->getScript($this->_relPath.'/declarationScript.php');
    }
    
    public function getMainLoopScript($id, $component){
        return $this->getScript($this->_relPath.'/mainLoopScript.php');
    }
    
    public function getWhileLoopScript($id, $component){
       return $this->getScript($this->_relPath.'/whileLoopScript.php');
    }
    
    public function getEndScript(){
        return $this->getScript($this->_relPath.'/endDeclarationScript.php');
    }
    
    final protected function getScript($path){
         if(file_exists($path)){
            ob_start();
            
            include $path;
            
            $script = ob_get_contents();
            ob_end_clean();
            return $script;
        }else{
            echo "File not found : $path\n";
        }
    }
    
    public function multiply(){
        return false;
    }
}

?>
