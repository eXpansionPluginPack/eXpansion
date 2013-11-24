<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Times\Gui\Widgets;

/**
 * Description of AudioPreload
 *
 * @author Reaby
 */
class AudioPreload extends \ManiaLive\Gui\Window {

    protected $audio;

    protected function onConstruct() {
        parent::onConstruct();
        $this->audio = new \ManiaLib\Gui\Elements\Audio();
        $this->audio->setPosY(260);
        $this->audio->setData("http://reaby.kapsi.fi/ml/ding.ogg");
        $this->addComponent($this->audio);
    }

}
