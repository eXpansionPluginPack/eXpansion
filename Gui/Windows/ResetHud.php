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
                        declare persistent Vec3[Text] windowLastPos;
                        declare persistent Vec3[Text] windowLastPosRel;
                        
                        windowLastPos.clear();
                        windowLastPosRel.clear();
                        yield;
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
