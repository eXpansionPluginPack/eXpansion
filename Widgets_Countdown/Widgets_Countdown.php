<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Countdown;

use ManiaLivePlugins\eXpansion\Core\types\config\Variable;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Widgets_Countdown\Gui\Widgets\Countdown;

/**
 * Description of Widgets_Countdown
 *
 * @author Petri
 */
class Widgets_Countdown extends ExpPlugin
{

    /** @var Config */
    private $config;

    private $settingsChanged = false;

    public function eXpOnReady()
    {
        $this->config = Config::GetInstance();
        $this->displayWidget();
        $this->enableApplicationEvents();
        $this->registerChatCommand("test", "displayWidget", 0, false);
    }

    public function onSettingsChanged(Variable $var)
    {
        $name = $var->getName();

        if (isset($this->config->$name)) {
            $this->settingsChanged = true;
        }
    }

    public function onPreLoop()
    {
        if ($this->settingsChanged) {
            $this->displayWidget();
            $this->settingsChanged = false;
        }
    }

    public function displayWidget()
    {
        Countdown::EraseAll();
        $widget = Countdown::Create(null, true);
        $widget->setPosition($this->config->posX, $this->config->posY);
        $widget->show();
    }

    public function eXpOnUnload()
    {
        WidgetAd::EraseAll();
        parent::eXpOnUnload();
    }

}
