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
                        declare persistent Vec3[Text][Text] exp_windowLastPos;
                        declare persistent Vec3[Text][Text] exp_windowLastPosRel;
                        declare persistent Vec3[Text][Text] exp_widgetLastPos;
                        declare persistent Vec3[Text][Text] exp_widgetLastPosRel;
                        declare persistent Boolean[Text][Text] exp_widgetVisible;
			declare persistent Text[Text][Text] exp_widgetLayers;
		
			
                        declare Text version = "' . \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION . '";
                        exp_windowLastPos[version].clear();
                        exp_windowLastPosRel[version].clear();
                        exp_widgetLastPos[version].clear();
                        exp_widgetLastPosRel[version].clear();
                        exp_widgetVisible[version].clear();
			exp_widgetLayers[version].clear();
			exp_widgetLayers[version].clear();
		
			
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
