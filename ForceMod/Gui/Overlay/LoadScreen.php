<?php

namespace ManiaLivePlugins\eXpansion\ForceMod\Gui\Overlay;

class LoadScreen extends \ManiaLive\Gui\Window {

    private $quad;

    protected function onConstruct() {
        $this->quad = new \ManiaLib\Gui\Elements\Quad(320, 180);
        $this->quad->setPosition(0, 0);
        $this->quad->setAlign("center", "center");
        $this->addComponent($this->quad);
        $this->quad->setPosZ(70);
    }

    public function setImage($image) {
        $this->quad->setImage($image, true);
    }

}

?>
