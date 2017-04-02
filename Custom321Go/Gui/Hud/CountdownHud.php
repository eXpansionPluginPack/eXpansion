<?php

namespace ManiaLivePlugins\eXpansion\Custom321Go\Gui\Hud;

use ManiaLib\Gui\Elements\Quad;
use ManiaLib\Gui\Elements\Video;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget;

/**
 * Description of HalloweenCountdown
 *
 * @author Petri Järvisalo <petri.jarvisalo@gmail.com>
 */
class CountdownHud extends PlainWidget
{

    protected $sprite;
    protected $video;

    public function onConstruct()
    {
        parent::onConstruct();
        $this->setName("countdown");
        $config = \ManiaLivePlugins\eXpansion\Custom321Go\Config::getInstance();

        $this->video = new Video(60, 30);
        $this->video->setPosition(0, 20);
        $this->video->setId("Countdown");
        $this->video->setAttribute("hidden", "1");
        $this->video->setAttribute("looping", "0");
        $this->video->setAttribute("play", "0");
        $this->video->setAlign("center", "center");
        $this->video->setData($config->video, true);
        $this->addComponent($this->video);

        $script = new Script("Custom321Go/Gui/Scripts");
        $this->registerScript($script);
    }
}
