<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

class ResetHud extends \ManiaLive\Gui\Window
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
        $this->xml->setContent('    
        <script><!--
                       main () {     
                        declare Vec3[Text][Text] exp_windowLastPos for LocalUser;
                        declare Vec3[Text][Text] exp_windowLastPosRel for LocalUser;
			
                        declare Vec3[Text][Text][Text] eXp_widgetLastPos for LocalUser;
                        declare Vec3[Text][Text][Text] eXp_widgetLastPosRel for LocalUser;
                        declare Boolean[Text][Text][Text] eXp_widgetVisible for LocalUser;
			declare Text[Text][Text][Text] eXp_widgetLayers for LocalUser;
			declare Boolean exp_needToCheckPersistentVars for UI = False;
			declare persistent Boolean exp_chatVisible = True;
			exp_chatVisible = True;
                        declare Text version = "' . \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION . '";
			
			if (exp_windowLastPos.existskey(version)) {
			    exp_windowLastPos[version].clear();
			}
			
			if (exp_windowLastPosRel.existskey(version)) {
			    exp_windowLastPosRel[version].clear();
			}                        
			if (eXp_widgetLastPos.existskey(version)) {
			    eXp_widgetLastPos[version].clear();
			}
			
			if (eXp_widgetLastPosRel.existskey(version)) {
			    eXp_widgetLastPosRel[version].clear();
			}
			
			if (eXp_widgetVisible.existskey(version)) {
			    eXp_widgetVisible[version].clear();
			}
			
			if (eXp_widgetLayers.existskey(version)) {
			    eXp_widgetLayers[version].clear();			
			}
		
			exp_needToCheckPersistentVars = True;
                       }
                --></script>');
        $this->addComponent($this->xml);
        parent::onDraw();
    }

    public function destroy()
    {
        parent::destroy();
    }
}
