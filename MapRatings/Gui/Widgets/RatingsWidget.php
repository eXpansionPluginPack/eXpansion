<?php

namespace ManiaLivePlugins\eXpansion\MapRatings\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;

class RatingsWidget extends \ManiaLive\Gui\Window {

    protected function onConstruct() {
        parent::onConstruct();
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
    }

    function onShow() {
        
    }

    function destroy() {
        parent::destroy();
    }

    function setStars($number) {
        $this->clearComponents();
        $frame = new \ManiaLive\Gui\Controls\Frame();
        $frame->setAlign("right","top");
        //$frame->setParentLayout(new \ManiaLib\Gui\Layouts\Line(5,5));
        print $number;
        for ($x = 1; $x <= round($number, 0); $x++) {
            $star = new \ManiaLib\Gui\Elements\Quad(3.5, 3.5);
            $star->setStyle("BgRaceScore2");
            $star->setSubStyle("Fame");
            $star->setPosX(-$x*3.5);
            $frame->addComponent($star);         
        }
        $this->addComponent($frame);
    }

}

?>
