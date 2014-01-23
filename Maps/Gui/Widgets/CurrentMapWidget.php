<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Widgets;

class CurrentMapWidget extends \ManiaLive\Gui\Window {

    protected $authorTime, $logo;

    protected function onConstruct() {
        $icon = new \ManiaLib\Gui\Elements\Quad(5, 5);
        $icon->setStyle("UIConstructionSimple_Buttons");
        $icon->setSubStyle("AuthorTime");
        $icon->setAlign("right", "center2");
        $icon->setPosition(5,-1);
        $this->addComponent($icon);

        $this->authorTime = new \ManiaLib\Gui\Elements\Label();
        $this->authorTime->setTextColor("fff");
        $this->authorTime->setTextPrefix('$s');
        $this->authorTime->setTextSize(1.5);
        $this->authorTime->setAlign("right", "top");
        $this->addComponent($this->authorTime);
    }

    function setMap(\Maniaplanet\DedicatedServer\Structures\Map $map) {
        $this->authorTime->setText(\ManiaLive\Utilities\Time::fromTM($map->authorTime));
    }

    function destroy() {
        parent::destroy();
    }

}

?>
