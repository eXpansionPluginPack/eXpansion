<?php

namespace ManiaLivePlugins\eXpansion\Widgets_ReadyState;

use ManiaLive\Gui\Group;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Widgets_ReadyState\Gui\Widgets\ReadyState;
use ManiaLivePlugins\eXpansion\Widgets_ReadyState\Gui\Widgets\ReadyWidget;

/**
 * Description of Widgets_Countdown
 *
 * @author Petri
 */
class Widgets_ReadyState extends ExpPlugin
{

    public $allPlayers = array();
    public $ready = array();
    public $lastReady = -1;


    public function eXpOnReady()
    {
        $this->update();
        $this->enableDedicatedEvents();
        $this->enableTickerEvent();
        $allPlayers = array_keys($this->storage->players + $this->storage->spectators);
        $this->allPlayers = $allPlayers;
        $this->ready = array();

    }


    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap)
    {
        $allPlayers = array_keys($this->storage->players + $this->storage->spectators);
        print_r($allPlayers);

        $this->allPlayers = $allPlayers;
        $this->ready = array();
        $this->lastReady = -1;
        $this->update();
    }

    public function onBeginMap($map, $warmUp, $matchContinuation)
    {
        $this->showWidget();
    }

    public function setReady($login)
    {
        $this->ready[] = $login;
    }

    public function onTick()
    {
        if (count($this->ready) != $this->lastReady) {
            $this->lastReady = count($this->ready);
            $this->showWidget();
        }
    }

    public function showWidget()
    {
        ReadyWidget::EraseAll();
        $group = Group::Create("admins", AdminGroups::getAdminsByPermission(Permission::CHAT_ADMINCHAT));
        $widget = ReadyWidget::create($group, true);
        $widget->setText("Ready: " . count($this->ready) . " Not ready: " . count(array_diff($this->ready, $this->allPlayers)));
        $widget->setPosition(-12, -75);
        $widget->setSize(60, 7);
        $widget->show();
    }


    public function update()
    {
        ReadyState::EraseAll();
        $widget = ReadyState::Create(null);
        $widget->setParent($this);
        $widget->show();
    }

    public function eXpOnUnload()
    {
        ReadyState::EraseAll();
        parent::eXpOnUnload();
    }

}
