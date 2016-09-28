<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

class HudMove extends \ManiaLive\Gui\Window
{

    private $xml;

    protected function onConstruct()
    {
        $this->xml = new \ManiaLive\Gui\Elements\Xml();
    }

    protected function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
    }

    public function onDraw()
    {
        $this->removeComponent($this->xml);
        $this->addComponent($this->xml);
        parent::onDraw();
    }

    public function enable()
    {
        $this->xml->setContent('    
        <script><!--
                       main () {     
                        declare Boolean exp_enableHudMove for UI = False;  
			declare Boolean exp_needToCheckPersistentVars for UI  = False;
			
                        exp_enableHudMove = True;           
			exp_needToCheckPersistentVars = True;
                       }
                --></script>');
    }

    public function disable()
    {
        $this->xml->setContent('    
        <script><!--
                       main () {
                        declare Boolean exp_enableHudMove for UI = False;     
			declare Boolean exp_needToCheckPersistentVars for UI = False;
			
                        exp_enableHudMove = False;          
			exp_needToCheckPersistentVars = True;
                       }
                --></script>');
    }

    public function destroy()
    {
        parent::destroy();
    }
}
