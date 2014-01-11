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
        $this->quad = new \ManiaLib\Gui\Elements\Quad(90,90);
        $this->quad->setAttribute("", $value);
    }

}
