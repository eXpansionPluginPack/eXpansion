<?php

namespace ManiaLivePlugins\eXpansion\Ants;

class Ants extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    public $wasWarmup = false;

    public function eXpOnReady()
    {
        parent::eXpOnReady();
        $this->enableDedicatedEvents();
        $config = Config::getInstance();
        \ManiaLivePlugins\eXpansion\Gui\Gui::preloadImage($config->texture);
        \ManiaLivePlugins\eXpansion\Gui\Gui::preloadUpdate();
        //  $this->registerChatCommand("ants", "ants");
    }

    public function ants()
    {
        Gui\Widget\AntsWidget::EraseAll();
        $window = Gui\Widget\AntsWidget::Create(null);
        $window->show();
    }

    public function onBeginMap($map, $warmUp, $matchContinuation)
    {
        Gui\Widget\AntsWidget::EraseAll();
    }

    public function onBeginMatch()
    {
        Gui\Widget\AntsWidget::EraseAll();
    }

    public function onBeginRound()
    {
        $this->wasWarmup = $this->connection->getWarmUp();
    }

    public function onEndMatch($rankings, $winnerTeamOrMap)
    {
        if ($this->wasWarmup) {
            return;
        }
        $window = Gui\Widget\AntsWidget::Create(null);
        $window->show();
    }

    public function eXpOnUnload()
    {
        Gui\Widget\AntsWidget::EraseAll();
        parent::eXpOnUnload();
    }
}
