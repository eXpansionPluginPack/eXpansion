<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

class ResetHud extends \ManiaLive\Gui\Window {

    private $xml;

    protected function onConstruct() {
        $this->xml = new \ManiaLive\Gui\Elements\Xml();
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
    }

    public function onDraw() {
        $this->removeComponent($this->xml);
        $this->xml->setContent('    
        <script><!--
                       main () {     
                        declare persistent Vec3[Text][Text][Text] eXp_windowLastPos;
                        declare persistent Vec3[Text][Text][Text] eXp_windowLastPosRel;
                        declare persistent Vec3[Text][Text][Text] eXp_widgetLastPos;
                        declare persistent Vec3[Text][Text][Text] eXp_widgetLastPosRel;
                        declare persistent Boolean[Text][Text][Text] eXp_widgetVisible;
			declare persistent Text[Text][Text][Text] eXp_widgetLayers;
			declare Boolean exp_needToCheckPersistentVars for UI = False;
			
                        declare Text version = "' . \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION . '";
                        exp_windowLastPos[version].clear();
                        exp_windowLastPosRel[version].clear();
                        exp_widgetLastPos[version].clear();
                        exp_widgetLastPosRel[version].clear();
                        exp_widgetVisible[version].clear();
			exp_widgetLayers[version].clear();
			exp_widgetLayers[version].clear();
		
			exp_needToCheckPersistentVars = True;
                       }
                --></script>');
        $this->addComponent($this->xml);
        parent::onDraw();
    }

    function destroy() {
        parent::destroy();
    }

}

?>
