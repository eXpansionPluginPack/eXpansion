<?php

namespace ManiaLivePlugins\eXpansion\Custom321Go\Gui\Hud;

use ManiaLib\Gui\Elements\Quad;
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
    protected $sprite2;

    public function onConstruct()
    {
        parent::onConstruct();
        $this->setName("countdown");
        $config = \ManiaLivePlugins\eXpansion\Custom321Go\Config::getInstance();

        $this->sprite = new Quad(60, 60);
        $this->sprite->setStyle("Bgs1InRace");
        $this->sprite->setSubStyle("BgEmpty");
        $this->sprite->setImage($config->sprite1, true);


        $this->sprite->setAlign("center", "center");
        $this->sprite->setId("sprite1");
        $this->addComponent($this->sprite);

        $this->sprite2 = new Quad(60, 60);
        $this->sprite2->setStyle("Bgs1InRace");
        $this->sprite2->setSubStyle("BgEmpty");
        $this->sprite2->setAlign("center", "center");
        $this->sprite2->setImage($config->sprite2, true);
        $this->sprite2->setId("sprite2");
        $this->addComponent($this->sprite2);

        $script = new Script("Custom321Go/Gui/Scripts");
        $this->registerScript($script);
    }
}
