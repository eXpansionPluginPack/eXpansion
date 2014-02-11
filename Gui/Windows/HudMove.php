<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

class HudMove extends \ManiaLive\Gui\Window {

    private $xml;

    protected function onConstruct() {
        $this->xml = new \ManiaLive\Gui\Elements\Xml();
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
    }

    public function onDraw() {
        $this->removeComponent($this->xml);
        $this->addComponent($this->xml);
        parent::onDraw();
    }

    public function enable() {
        $this->xml->setContent('    
        <script><!--
                       main () {     
                        declare persistent Boolean exp_enableHudMove = False;  
			declare persistent Boolean exp_needToCheckPersistentVars = False;
			
                        exp_enableHudMove = True;           
			exp_needToCheckPersistentVars = True;
                       }
                --></script>');
    }

    public function disable() {
        $this->xml->setContent('    
        <script><!--
                       main () {
                        declare persistent Boolean exp_enableHudMove = False;     
			declare persistent Boolean exp_needToCheckPersistentVars = False;
			
                        exp_enableHudMove = False;          
			exp_needToCheckPersistentVars = True;
                       }
                --></script>');
    }

    function destroy() {
        parent::destroy();
    }

}

?>
