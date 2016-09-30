<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Countdown\Gui\Widgets;

use ManiaLib\Gui\Elements\Label;
use ManiaLivePlugins\eXpansion\Gui\Script_libraries\TimeExt;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Widgets_Countdown\Config;

/**
 * Description of Countdown
 *
 * @author Petri
 */
class Countdown extends \ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget
{
    protected $label;
    protected $script;


    protected function onConstruct()
    {
        parent::onConstruct();
        $config = Config::getInstance();

        $this->event = new Label(90, 7);
        $this->event->setAlign("center", "top");
        $this->event->setPosition($this->getRealSizeX() / 2, 0);
        $this->event->setStyle(\ManiaLib\Gui\Elements\Format::TextRaceMessageBig);
        $this->event->setTextSize(2);
        $this->event->setText($config->eventName);

        $this->label = new Label();
        $this->label->setPosition(0, -6);
        $this->label->setStyle(\ManiaLib\Gui\Elements\Format::TextRaceChat);
        $this->label->setId("countdown");
        $this->addComponent($this->label);

        $this->script = new TimeExt();
        $this->registerScript($this->script);

        $this->script = new Script("Widgets_Countdown\Gui\Script");
        $this->script->setParam("stamp", (int)$config->time);
        $this->script->setParam("timezone", date_default_timezone_get());
        $this->registerScript($this->script);
    }
}
