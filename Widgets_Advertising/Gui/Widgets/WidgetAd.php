<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Advertising\Gui\Widgets;

/**
 * Description of WidgetAd
 *
 * @author Petri
 */
class WidgetAd extends \ManiaLive\Gui\Window {

    protected $quad;

    protected function onConstruct() {
        $this->quad = new \ManiaLib\Gui\Elements\Quad();
        $this->quad->setAlign("center", "top");
        $this->addComponent($this->quad);
    }

    public function setImage($image, $imageFocus, $url) {
        $this->quad->setImage($image, true);
        $this->quad->setImageFocus($imageFocus, true);
        $this->quad->setUrl($url);
    }

    public function setImageSize($sizeX, $sizeY, $preferredSize) {
        $wantedAspectRatio = $sizeX / $sizeY;
        $currentAspectRatio = 16 / 9;
        $x = $preferredSize * ($currentAspectRatio / $wantedAspectRatio) * $wantedAspectRatio;
        $y = $preferredSize * ($currentAspectRatio / $wantedAspectRatio);        
        $this->quad->setSize($x, $y);
    }

}
